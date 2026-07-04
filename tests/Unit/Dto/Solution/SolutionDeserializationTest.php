<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Solution;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Solution\GeeTestSolution;
use CapMonsterClient\Dto\Solution\HCaptchaSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Solution\ReCaptchaSolution;
use CapMonsterClient\Dto\Solution\TextSolution;
use CapMonsterClient\Dto\Solution\TokenSolution;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\ArrayTransformerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class SolutionDeserializationTest extends TestCase
{
    private ArrayTransformerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = (new SerializerBuilder())->build();
    }

    // ------------------------------------------------------------------ TokenSolution (3 wire shapes)

    public function testTokenSolutionFromTokenAndUserAgent(): void
    {
        $solution = $this->serializer->fromArray(['token' => 'T', 'userAgent' => 'UA'], TokenSolution::class);

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('T', $solution->getToken());
        self::assertSame('UA', $solution->getUserAgent());
        self::assertNull($solution->getCfClearance());
    }

    public function testTokenSolutionFromCfClearanceOnly(): void
    {
        $solution = $this->serializer->fromArray(['cf_clearance' => 'CF123'], TokenSolution::class);

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('CF123', $solution->getCfClearance());
        self::assertNull($solution->getToken());
        self::assertNull($solution->getUserAgent());
    }

    public function testTokenSolutionFromTokenOnly(): void
    {
        $solution = $this->serializer->fromArray(['token' => 'ONLY'], TokenSolution::class);

        self::assertInstanceOf(TokenSolution::class, $solution);
        self::assertSame('ONLY', $solution->getToken());
        self::assertNull($solution->getUserAgent());
        self::assertNull($solution->getCfClearance());
    }

    // ------------------------------------------------------------------ GeeTestSolution v3 / v4

    public function testGeeTestSolutionVersion3Shape(): void
    {
        $solution = $this->serializer->fromArray(
            ['challenge' => 'CH', 'validate' => 'VA', 'seccode' => 'SC'],
            GeeTestSolution::class
        );

        self::assertInstanceOf(GeeTestSolution::class, $solution);
        self::assertSame('CH', $solution->getChallenge());
        self::assertSame('VA', $solution->getValidate());
        self::assertSame('SC', $solution->getSeccode());
        self::assertNull($solution->getCaptchaId());
        self::assertNull($solution->getLotNumber());
        self::assertNull($solution->getPassToken());
        self::assertNull($solution->getGenTime());
        self::assertNull($solution->getCaptchaOutput());
    }

    public function testGeeTestSolutionVersion4Shape(): void
    {
        $solution = $this->serializer->fromArray(
            [
                'captcha_id' => 'CI',
                'lot_number' => 'LN',
                'pass_token' => 'PT',
                'gen_time' => 'GT',
                'captcha_output' => 'CO',
            ],
            GeeTestSolution::class
        );

        self::assertInstanceOf(GeeTestSolution::class, $solution);
        self::assertSame('CI', $solution->getCaptchaId());
        self::assertSame('LN', $solution->getLotNumber());
        self::assertSame('PT', $solution->getPassToken());
        self::assertSame('GT', $solution->getGenTime());
        self::assertSame('CO', $solution->getCaptchaOutput());
        self::assertNull($solution->getChallenge());
        self::assertNull($solution->getValidate());
        self::assertNull($solution->getSeccode());
    }

    // ------------------------------------------------------------------ other solutions

    public function testTextSolution(): void
    {
        $solution = $this->serializer->fromArray(['text' => 'answer42'], TextSolution::class);

        self::assertInstanceOf(TextSolution::class, $solution);
        self::assertSame('answer42', $solution->getText());
    }

    public function testReCaptchaSolution(): void
    {
        $solution = $this->serializer->fromArray(
            ['gRecaptchaResponse' => str_repeat('g', 500)],
            ReCaptchaSolution::class
        );

        self::assertInstanceOf(ReCaptchaSolution::class, $solution);
        self::assertSame(str_repeat('g', 500), $solution->getGRecaptchaResponse());
    }

    public function testHCaptchaSolutionDeserializesDocumentedCamelCaseKeys(): void
    {
        $solution = $this->serializer->fromArray(
            ['gRecaptchaResponse' => 'G', 'userAgent' => 'UA', 'respKey' => 'RK'],
            HCaptchaSolution::class
        );

        self::assertInstanceOf(HCaptchaSolution::class, $solution);
        self::assertSame('G', $solution->getGRecaptchaResponse());
        self::assertSame('UA', $solution->getUserAgent());
        self::assertSame('RK', $solution->getRespKey());
    }

    public function testHCaptchaSolutionIgnoresSnakeCaseKeys(): void
    {
        // The CapMonster API only ever returns camelCase keys; snake_case keys must not map.
        $solution = $this->serializer->fromArray(
            ['g_recaptcha_response' => 'G', 'user_agent' => 'UA', 'resp_key' => 'RK'],
            HCaptchaSolution::class
        );

        self::assertInstanceOf(HCaptchaSolution::class, $solution);
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');
        $solution->getGRecaptchaResponse();
    }

    public function testRawSolutionCarriesSolutionPayloadThroughApiClient(): void
    {
        // The API "solution" object has no "payload" wrapper — its fields ARE the payload,
        // so ApiClient::extractTaskSolution() constructs RawSolution directly from the raw array.
        $apiClient = new ApiClient(
            new class () implements ClientInterface {
                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    throw new \LogicException('No HTTP request expected in this test');
                }
            },
            new CapMonsterConfiguration('KEY')
        );
        $response = $this->serializer->fromArray(
            ['errorId' => 0, 'status' => 'ready', 'solution' => ['answer' => ['x' => 1]]],
            GetTaskResultResponse::class
        );
        assert($response instanceof GetTaskResultResponse);

        $solution = $apiClient->extractTaskSolution(TypeTask::DATADOME_TASK, $response);

        self::assertInstanceOf(RawSolution::class, $solution);
        self::assertSame(['answer' => ['x' => 1]], $solution->getPayload());
    }

    public function testRawSolutionDirectConstruction(): void
    {
        $solution = new RawSolution(['cookie' => 'datadome=x']);

        self::assertSame(['cookie' => 'datadome=x'], $solution->getPayload());
    }
}
