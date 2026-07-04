<?php

declare(strict_types=1);

namespace Tests\Integration;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Tests\Support\RecordingHandler;
use Webclient\Fake\FakeHttpClient;

final class ApiClientSolutionAndUserAgentTest extends TestCase
{
    private RecordingHandler $handler;

    private ApiClient $apiClient;

    protected function setUp(): void
    {
        $this->handler = new RecordingHandler();
        $this->apiClient = new ApiClient(
            new FakeHttpClient($this->handler),
            new CapMonsterConfiguration('KEY')
        );
    }

    /**
     * @param array<string, mixed> $solution
     */
    private function readyResponse(array $solution): GetTaskResultResponse
    {
        $response = (new SerializerBuilder())->build()->fromArray(
            ['errorId' => 0, 'status' => 'ready', 'solution' => $solution],
            GetTaskResultResponse::class
        );
        assert($response instanceof GetTaskResultResponse);

        return $response;
    }

    // ------------------------------------------------------------------ extractTaskSolution

    public function testExtractsTokenSolutionForTurnstile(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::TURNSTILE_TASK,
            $this->readyResponse(['token' => 'TKN', 'userAgent' => 'UA'])
        );

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('TKN', $solution->getToken());
        self::assertSame('UA', $solution->getUserAgent());
    }

    public function testExtractsCfClearanceTokenSolutionForTurnstileChallenge(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::TURNSTILE_CHALLENGE_TASK,
            $this->readyResponse(['cf_clearance' => 'CF'])
        );

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('CF', $solution->getCfClearance());
        self::assertNull($solution->getToken());
    }

    public function testExtractsTextSolutionForImageToText(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::IMAGE_TO_TEXT_TASK,
            $this->readyResponse(['text' => 'abc123'])
        );

        self::assertInstanceOf(TextSolution::class, $solution);
        self::assertSame('abc123', $solution->getText());
    }

    public function testExtractsReCaptchaSolutionForNoCaptcha(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::NO_CAPTCHA_TASK_PROXYLESS,
            $this->readyResponse(['gRecaptchaResponse' => 'GRC'])
        );

        self::assertInstanceOf(ReCaptchaSolution::class, $solution);
        self::assertSame('GRC', $solution->getGRecaptchaResponse());
    }

    public function testExtractsGeeTestV4Solution(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::GEE_TEST_TASK_PROXYLESS,
            $this->readyResponse([
                'captcha_id' => 'CI',
                'lot_number' => 'LN',
                'pass_token' => 'PT',
                'gen_time' => 'GT',
                'captcha_output' => 'CO',
            ])
        );

        self::assertInstanceOf(GeeTestSolution::class, $solution);
        self::assertSame('LN', $solution->getLotNumber());
        self::assertSame('CO', $solution->getCaptchaOutput());
    }

    public function testExtractsHCaptchaSolutionWithCamelCaseKeys(): void
    {
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::H_CAPTCHA_TASK_PROXYLESS,
            $this->readyResponse(['gRecaptchaResponse' => 'G', 'userAgent' => 'UA', 'respKey' => 'RK'])
        );

        self::assertInstanceOf(HCaptchaSolution::class, $solution);
        self::assertSame('G', $solution->getGRecaptchaResponse());
        self::assertSame('UA', $solution->getUserAgent());
        self::assertSame('RK', $solution->getRespKey());
    }

    public function testExtractsRawSolutionForDataDome(): void
    {
        $solutionFields = ['domains' => ['example.com' => ['cookies' => ['datadome' => 'dd=1']]]];
        $solution = $this->apiClient->extractTaskSolution(
            TypeTask::DATADOME_TASK,
            $this->readyResponse($solutionFields)
        );

        self::assertInstanceOf(RawSolution::class, $solution);
        self::assertSame($solutionFields, $solution->getPayload());
    }

    public function testExtractsRawSolutionForBinanceAndAlibaba(): void
    {
        $solutionFields = ['token' => 'T', 'userAgent' => 'UA'];

        foreach ([TypeTask::BINANCE_TASK, TypeTask::ALIBABA_TASK] as $typeTask) {
            $solution = $this->apiClient->extractTaskSolution($typeTask, $this->readyResponse($solutionFields));

            self::assertInstanceOf(RawSolution::class, $solution);
            self::assertSame($solutionFields, $solution->getPayload());
        }
    }

    // ------------------------------------------------------------------ getActualUserAgent

    public function testGetActualUserAgentIssuesGetToDedicatedEndpointAndTrims(): void
    {
        $this->handler->pushText("  Mozilla/5.0 (X11; Linux x86_64) Chrome/126.0  \n");

        $userAgent = $this->apiClient->getActualUserAgent();

        self::assertSame('Mozilla/5.0 (X11; Linux x86_64) Chrome/126.0', $userAgent);
        $request = $this->handler->request(0);
        self::assertSame('GET', $request['method']);
        self::assertSame('https://capmonster.cloud/api/useragent/actual', $request['uri']);
    }

    public function testGetActualUserAgentEmptyBodyIsResponseError(): void
    {
        $this->handler->pushText("   \n");

        try {
            $this->apiClient->getActualUserAgent();
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::RESPONSE_ERROR, $exception->getType());
        }
    }

    public function testGetActualUserAgentNon2xxIsResponseCodeError(): void
    {
        $this->handler->pushText('nope', 404);

        try {
            $this->apiClient->getActualUserAgent();
            self::fail('Expected CapMonsterException was not thrown');
        } catch (CapMonsterException $exception) {
            self::assertSame(ErrorType::RESPONSE_CODE_ERROR, $exception->getType());
        }
    }
}
