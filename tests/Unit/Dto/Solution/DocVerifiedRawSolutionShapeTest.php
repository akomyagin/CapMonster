<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Solution;

use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use JMS\Serializer\ArrayTransformerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Pins the *documented* `solution` object of every RawSolution-mapped task type to the literal
 * JSON quoted verbatim from the CapMonster docs (see docs/API_CONTRACT.md). RawSolution is a
 * loss-less passthrough, so the assertion is that `getPayload()` returns the solution object
 * exactly as the API sends it — no key renaming, no flattening, nested structure intact.
 */
final class DocVerifiedRawSolutionShapeTest extends TestCase
{
    private ArrayTransformerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = (new SerializerBuilder())->build();
    }

    /**
     * @return iterable<string, array{TypeTask, array<string, mixed>}>
     */
    public static function provideDocumentedSolutions(): iterable
    {
        // DataDome — docs/captchas/datadome.mdx
        yield 'DataDome' => [TypeTask::DATADOME_TASK, [
            'domains' => ['www.example.com' => ['cookies' => ['datadome' => 'P1w0...LhIkhm'], 'localStorage' => null]],
            'url' => null, 'fingerprint' => null, 'headers' => null, 'data' => null,
        ]];
        // Amazon — docs/captchas/amazon-task.mdx
        yield 'Amazon' => [TypeTask::AMAZON_TASK, [
            'cookies' => ['aws-waf-token' => '10115f5b-ebd8-45c7-851e-cfd4f6a82e3e:EAoAua1QezAhAAAA:dp7sp2rX'],
            'userAgent' => 'userAgentPlaceholder',
        ]];
        // Binance — docs/captchas/binance.mdx
        yield 'Binance' => [TypeTask::BINANCE_TASK, [
            'token' => 'captcha#09ba4905a79f44f2a99e44f234439644-ioVA7neog7eRHCDAsC0Mix',
            'userAgent' => 'userAgentPlaceholder',
        ]];
        // MTCaptcha — docs/captchas/mtcaptcha-task.mdx
        yield 'MTCaptcha' => [TypeTask::MT_CAPTCHA_TASK, [
            'token' => 'v1(155506dc,c8c2e356,MTPublic-abCDEFJAB,70f03532a53...5FSDA**)',
        ]];
        // Yidun — docs/captchas/yidun-task.mdx
        yield 'Yidun' => [TypeTask::YIDUN_TASK, [
            'token' => 'CN31_9AwsPmaYcJameP_09rA0vkVMQsPij...RXTlFJFc3',
        ]];
        // Alibaba — docs/captchas/alibaba-task.mdx
        yield 'Alibaba' => [TypeTask::ALIBABA_TASK, [
            'data' => ['tokens' => '{"sceneId":"1ww7426c4","certifyId":"kBjCxX2W2c"}'],
        ]];
        // Basilisk — docs/captchas/Basilisk-task.mdx
        yield 'Basilisk' => [TypeTask::BASILISK_TASK, [
            'data' => ['captcha_response' => '5620301f30daf284b829fba66fa9b3d0'],
            'headers' => ['User-Agent' => 'userAgentPlaceholder'],
        ]];
        // TenDI — docs/captchas/tendi.mdx
        yield 'TenDI' => [TypeTask::TENDI_TASK, [
            'data' => ['randstr' => '@EcL', 'ticket' => 'tr03lHUhdnuW3neJZu.....7LrIbs*'],
            'headers' => ['User-Agent' => 'userAgentPlaceholder'],
        ]];
        // Imperva / Incapsula — docs/captchas/incapsula.mdx
        yield 'Imperva' => [TypeTask::IMPERVA_TASK, [
            'domains' => ['https://example.com' => ['cookies' => ['___utmvc' => 'NMB+nRa4inxX...']]],
        ]];
        // TSPD — docs/captchas/tspd-task.mdx (note capitalised Domains/Cookies)
        yield 'TSPD' => [TypeTask::TSPD_TASK, [
            'Domains' => ['example.com' => ['Cookies' => ['TS386a400d029' => '08267...01a06e']]],
        ]];
        // Hunt — docs/captchas/hunt-task.mdx
        yield 'Hunt' => [TypeTask::HUNT_TASK, [
            'data' => ['token' => '6IyDCCpDdSK...YGs1Wug/z/kLNSpjewI='],
        ]];
        // Altcha — docs/captchas/altcha-task.mdx (number is an int)
        yield 'Altcha' => [TypeTask::ALTCHA_TASK, [
            'data' => ['token' => 'eyJhbGdvcml...Ljc2MDEzM30=', 'number' => 44619],
        ]];
        // ComplexImage — docs/captchas/compleximage/** (grid variant)
        yield 'ComplexImage' => [TypeTask::COMPLEX_IMAGE_TASK, [
            'answer' => [true, true, false, false, true, false, false, true, true],
            'metadata' => ['AnswerType' => 'Grid'],
        ]];
    }

    /**
     * @param array<string, mixed> $documentedSolution
     */
    #[DataProvider('provideDocumentedSolutions')]
    public function testRawSolutionPassesDocumentedSolutionThroughUnchanged(
        TypeTask $typeTask,
        array $documentedSolution
    ): void {
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
            ['errorId' => 0, 'status' => 'ready', 'solution' => $documentedSolution],
            GetTaskResultResponse::class
        );
        assert($response instanceof GetTaskResultResponse);

        $solution = $apiClient->extractTaskSolution($typeTask, $response);

        self::assertInstanceOf(RawSolution::class, $solution);
        self::assertSame($documentedSolution, $solution->getPayload());
    }
}
