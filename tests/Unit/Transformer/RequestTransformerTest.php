<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Dto\Task\AlibabaTask;
use CapMonsterClient\Dto\Task\AltchaTask;
use CapMonsterClient\Dto\Task\AmazonTask;
use CapMonsterClient\Dto\Task\BasiliskTask;
use CapMonsterClient\Dto\Task\BinanceTask;
use CapMonsterClient\Dto\Task\CastleTask;
use CapMonsterClient\Dto\Task\ComplexImageTask;
use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\FunCaptchaTask;
use CapMonsterClient\Dto\Task\GeeTestTask;
use CapMonsterClient\Dto\Task\HCaptchaTask;
use CapMonsterClient\Dto\Task\HuntTask;
use CapMonsterClient\Dto\Task\ImageToTextTask;
use CapMonsterClient\Dto\Task\ImpervaTask;
use CapMonsterClient\Dto\Task\MTCaptchaTask;
use CapMonsterClient\Dto\Task\NoCaptchaTask;
use CapMonsterClient\Dto\Task\ProsopoTask;
use CapMonsterClient\Dto\Task\ProxySetting;
use CapMonsterClient\Dto\Task\RecaptchaV2EnterpriseTask;
use CapMonsterClient\Dto\Task\RecaptchaV3EnterpriseTask;
use CapMonsterClient\Dto\Task\RecaptchaV3TaskProxyless;
use CapMonsterClient\Dto\Task\TenDITask;
use CapMonsterClient\Dto\Task\TSPDTask;
use CapMonsterClient\Dto\Task\TurnstileChallengeTask;
use CapMonsterClient\Dto\Task\TurnstileTask;
use CapMonsterClient\Dto\Task\TurnstileWaitingRoomTask;
use CapMonsterClient\Dto\Task\YidunTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\RequestTransformer;
use PHPUnit\Framework\TestCase;

final class RequestTransformerTest extends TestCase
{
    private const URL = 'https://x.test/';
    private const KEY = 'sitekey';

    private RequestTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new RequestTransformer(new SerializerBuilder());
    }

    private static function proxy(): ProxySetting
    {
        return ProxySetting::create('http', '1.2.3.4', 8080, 'user', 'pass');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(AbstractTask $task, ?string $callbackUrl = null): array
    {
        $json = $this->transformer->transform(new CreateTaskRequest('KEY', $task, $callbackUrl));

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    private function taskPayload(AbstractTask $task): array
    {
        return $this->payload($task)['task'];
    }

    // ------------------------------------------------------------------ envelope

    public function testGetBalanceRequestContainsOnlyClientKey(): void
    {
        $json = $this->transformer->transform(new GetBalanceRequest('KEY'));

        self::assertSame('{"clientKey":"KEY"}', $json);
    }

    public function testGetTaskResultRequestContainsClientKeyAndTaskId(): void
    {
        $task = new ImageToTextTask('B64');
        $task->setTaskId(42);
        $json = $this->transformer->transform(new GetTaskResultRequest('KEY', $task));

        self::assertSame('{"clientKey":"KEY","taskId":42}', $json);
    }

    public function testCreateTaskRequestIncludesCallbackUrlWhenSet(): void
    {
        $payload = $this->payload(new ImageToTextTask('B64'), 'https://cb.test/hook');

        self::assertSame('https://cb.test/hook', $payload['callbackUrl']);
    }

    public function testCreateTaskRequestOmitsCallbackUrlWhenAbsent(): void
    {
        self::assertArrayNotHasKey('callbackUrl', $this->payload(new ImageToTextTask('B64')));
    }

    public function testTaskIdIsNeverSerializedIntoTaskObject(): void
    {
        $task = new ImageToTextTask('B64');
        $task->setTaskId(77);

        self::assertArrayNotHasKey('taskId', $this->taskPayload($task));
    }

    // ------------------------------------------------------------------ type aliases

    public function testNoCaptchaProxylessIsAliasedToRecaptchaV2TaskProxyless(): void
    {
        $task = new NoCaptchaTask(self::URL, self::KEY);

        self::assertSame('RecaptchaV2TaskProxyless', $this->taskPayload($task)['type']);
    }

    public function testNoCaptchaWithProxyIsAliasedToRecaptchaV2Task(): void
    {
        $task = new NoCaptchaTask(self::URL, self::KEY, proxySetting: self::proxy());

        self::assertSame('RecaptchaV2Task', $this->taskPayload($task)['type']);
    }

    public function testTurnstileTaskKeepsWireTypeTurnstileTask(): void
    {
        $task = new TurnstileTask(self::URL, self::KEY);

        self::assertSame('TurnstileTask', $this->taskPayload($task)['type']);
    }

    public function testTurnstileChallengeTaskIsAliasedToTurnstileTask(): void
    {
        $task = new TurnstileChallengeTask(self::URL, self::KEY, 'token');

        self::assertSame('TurnstileTask', $this->taskPayload($task)['type']);
    }

    public function testTurnstileWaitingRoomTaskIsAliasedToTurnstileTask(): void
    {
        $task = new TurnstileWaitingRoomTask(self::URL, self::KEY, 'html64');

        self::assertSame('TurnstileTask', $this->taskPayload($task)['type']);
    }

    // ------------------------------------------------------------------ turnstile payloads (proxy regression guard)

    public function testTurnstileTaskWithProxyIncludesAllProxyFields(): void
    {
        $task = new TurnstileTask(self::URL, self::KEY, proxySetting: self::proxy());

        self::assertSame(
            [
                'type' => 'TurnstileTask',
                'proxyType' => 'http',
                'proxyAddress' => '1.2.3.4',
                'proxyPort' => 8080,
                'proxyLogin' => 'user',
                'proxyPassword' => 'pass',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
            ],
            $this->taskPayload($task)
        );
    }

    public function testTurnstileTaskProxylessFullPayload(): void
    {
        $task = new TurnstileTask(self::URL, self::KEY, 'act', 'data', 'UA');

        self::assertSame(
            [
                'type' => 'TurnstileTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'pageAction' => 'act',
                'data' => 'data',
            ],
            $this->taskPayload($task)
        );
    }

    public function testTurnstileChallengeTaskFullPayload(): void
    {
        $task = new TurnstileChallengeTask(
            self::URL,
            self::KEY,
            'cf_clearance',
            'act',
            'pd',
            'data',
            'html64',
            'UA',
            self::proxy()
        );

        self::assertSame(
            [
                'type' => 'TurnstileTask',
                'proxyType' => 'http',
                'proxyAddress' => '1.2.3.4',
                'proxyPort' => 8080,
                'proxyLogin' => 'user',
                'proxyPassword' => 'pass',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'cloudflareTaskType' => 'cf_clearance',
                'pageAction' => 'act',
                'pageData' => 'pd',
                'data' => 'data',
                'htmlPageBase64' => 'html64',
            ],
            $this->taskPayload($task)
        );
    }

    public function testTurnstileWaitingRoomTaskFullPayload(): void
    {
        $task = new TurnstileWaitingRoomTask(self::URL, self::KEY, 'html64', 'UA');

        self::assertSame(
            [
                'type' => 'TurnstileTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'cloudflareTaskType' => 'wait_room',
                'htmlPageBase64' => 'html64',
            ],
            $this->taskPayload($task)
        );
    }

    // ------------------------------------------------------------------ image tasks strip websiteURL/websiteKey

    public function testImageToTextTaskHasNoWebsiteUrlOrKey(): void
    {
        $payload = $this->taskPayload(new ImageToTextTask('B64'));

        self::assertArrayNotHasKey('websiteURL', $payload);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertSame(['type' => 'ImageToTextTask', 'body' => 'B64'], $payload);
    }

    public function testImageToTextTaskFullPayload(): void
    {
        $task = new ImageToTextTask('B64', 'yandex', 80, true, 1, false);

        self::assertSame(
            [
                'type' => 'ImageToTextTask',
                'body' => 'B64',
                'capMonsterModule' => 'yandex',
                'recognizingThreshold' => 80,
                'case' => true,
                'numeric' => 1,
                'math' => false,
            ],
            $this->taskPayload($task)
        );
    }

    public function testComplexImageTaskDocShape(): void
    {
        // Doc-verified wire shape (docs/captchas/compleximage/**):
        // {"type":"ComplexImageTask","class":"recognition","imagesBase64":[...],"metadata":{"Task":"baidu"}}
        $payload = $this->taskPayload(new ComplexImageTask(['base64string'], 'baidu'));

        self::assertArrayNotHasKey('websiteURL', $payload);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertArrayNotHasKey('body', $payload);
        self::assertArrayNotHasKey('module', $payload);
        self::assertSame(
            [
                'type' => 'ComplexImageTask',
                'class' => 'recognition',
                'metadata' => ['Task' => 'baidu'],
                'imagesBase64' => ['base64string'],
            ],
            $payload
        );
    }

    public function testComplexImageTaskMergesExtraMetadataKeepingTask(): void
    {
        $payload = $this->taskPayload(
            new ComplexImageTask(['b64a', 'b64b'], 'dli', '{"TaskArgument":"red"}')
        );

        self::assertSame(['b64a', 'b64b'], $payload['imagesBase64']);
        self::assertSame(['Task' => 'dli', 'TaskArgument' => 'red'], $payload['metadata']);
    }

    // ------------------------------------------------------------------ recaptcha family

    public function testNoCaptchaProxylessFullPayload(): void
    {
        $task = new NoCaptchaTask(self::URL, self::KEY, 'dataS', true, 'UA', 'a=b');

        self::assertSame(
            [
                'type' => 'RecaptchaV2TaskProxyless',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'cookies' => 'a=b',
                'recaptchaDataSValue' => 'dataS',
                'isInvisible' => true,
            ],
            $this->taskPayload($task)
        );
    }

    public function testRecaptchaV3TaskProxylessFullPayload(): void
    {
        $task = new RecaptchaV3TaskProxyless(self::URL, self::KEY, 0.5, 'login');

        self::assertSame(
            [
                'type' => 'RecaptchaV3TaskProxyless',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'minScore' => 0.5,
                'pageAction' => 'login',
            ],
            $this->taskPayload($task)
        );
    }

    public function testRecaptchaV2EnterpriseTaskSwitchesTypeOnProxy(): void
    {
        $proxyless = new RecaptchaV2EnterpriseTask(self::URL, self::KEY);
        $withProxy = new RecaptchaV2EnterpriseTask(
            self::URL,
            self::KEY,
            proxySetting: self::proxy()
        );

        self::assertSame('RecaptchaV2EnterpriseTaskProxyless', $this->taskPayload($proxyless)['type']);
        self::assertSame('RecaptchaV2EnterpriseTask', $this->taskPayload($withProxy)['type']);
    }

    public function testRecaptchaV2EnterpriseTaskProxylessFullPayload(): void
    {
        $task = new RecaptchaV2EnterpriseTask(self::URL, self::KEY, 'payload', 'www.google.com', 'act');

        self::assertSame(
            [
                'type' => 'RecaptchaV2EnterpriseTaskProxyless',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'enterprisePayload' => 'payload',
                'apiDomain' => 'www.google.com',
                'pageAction' => 'act',
            ],
            $this->taskPayload($task)
        );
    }

    public function testRecaptchaV3EnterpriseTaskFullPayload(): void
    {
        $task = new RecaptchaV3EnterpriseTask(self::URL, self::KEY, 0.7, 'act', 'pl', 'dom');

        self::assertSame(
            [
                'type' => 'RecaptchaV3EnterpriseTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'minScore' => 0.7,
                'pageAction' => 'act',
                'enterprisePayload' => 'pl',
                'apiDomain' => 'dom',
            ],
            $this->taskPayload($task)
        );
    }

    // ------------------------------------------------------------------ funcaptcha rename

    public function testFunCaptchaTaskRenamesWebsiteKeyToWebsitePublicKey(): void
    {
        $task = new FunCaptchaTask(self::URL, 'pubkey', 'sub.example', '{"blob":"v"}');
        $payload = $this->taskPayload($task);

        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertArrayNotHasKey('website_public_key', $payload);
        self::assertArrayNotHasKey('fun_captcha_api_js_subdomain', $payload);
        self::assertSame(
            [
                'type' => 'FunCaptchaTaskProxyless',
                'websiteURL' => self::URL,
                'funcaptchaApiJSSubdomain' => 'sub.example',
                'data' => '{"blob":"v"}',
                'websitePublicKey' => 'pubkey',
            ],
            $payload
        );
    }

    public function testFunCaptchaTaskWithProxyFullPayload(): void
    {
        $task = new FunCaptchaTask(self::URL, 'pubkey', proxySetting: self::proxy());

        self::assertSame(
            [
                'type' => 'FunCaptchaTask',
                'proxyType' => 'http',
                'proxyAddress' => '1.2.3.4',
                'proxyPort' => 8080,
                'proxyLogin' => 'user',
                'proxyPassword' => 'pass',
                'websiteURL' => self::URL,
                'websitePublicKey' => 'pubkey',
            ],
            $this->taskPayload($task)
        );
    }

    // ------------------------------------------------------------------ hcaptcha

    public function testHCaptchaTaskProxylessFullPayload(): void
    {
        $task = new HCaptchaTask(self::URL, self::KEY, true, 'data', true, 'UA');

        self::assertSame(
            [
                'type' => 'HCaptchaTaskProxyless',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'isInvisible' => true,
                'data' => 'data',
                'fallbackToActualUA' => true,
            ],
            $this->taskPayload($task)
        );
    }

    // ------------------------------------------------------------------ geetest v3 / v4

    public function testGeeTestVersion3FullPayload(): void
    {
        $task = new GeeTestTask(self::URL, 'GT', 'CHAL', 'api.geetest.com', '{"a":1}');
        $payload = $this->taskPayload($task);

        // Doc-verified (docs/captchas/geetest-task.mdx): the domain key travels
        // ONLY as "gt" — there is no "websiteKey" in the GeeTest wire payload.
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertSame(
            [
                'type' => 'GeeTestTaskProxyless',
                'websiteURL' => self::URL,
                'gt' => 'GT',
                'challenge' => 'CHAL',
                'geetestApiServerSubdomain' => 'api.geetest.com',
                'geetestGetLib' => '{"a":1}',
                'version' => 3,
            ],
            $payload
        );
    }

    public function testGeeTestVersion4PayloadHasInitParametersAndNoChallenge(): void
    {
        $task = new GeeTestTask(self::URL, 'GT', null, null, null, 4, 'slide');
        $payload = $this->taskPayload($task);

        self::assertArrayNotHasKey('challenge', $payload);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertSame(
            [
                'type' => 'GeeTestTaskProxyless',
                'websiteURL' => self::URL,
                'initParameters' => ['riskType' => 'slide'],
                'gt' => 'GT',
                'version' => 4,
            ],
            $payload
        );
    }

    public function testGeeTestWithProxySwitchesTypeAndIncludesProxyFields(): void
    {
        $task = new GeeTestTask(self::URL, 'GT', 'CHAL', proxySetting: self::proxy());
        $payload = $this->taskPayload($task);

        self::assertSame('GeeTestTask', $payload['type']);
        self::assertSame('GT', $payload['gt']);
        self::assertSame('http', $payload['proxyType']);
        self::assertSame('1.2.3.4', $payload['proxyAddress']);
        self::assertSame(8080, $payload['proxyPort']);
    }

    // ------------------------------------------------------------------ CustomTask family (websiteKey/cookies stripping)

    public function testDataDomeTaskFullPayload(): void
    {
        $task = new DataDomeTask(
            self::URL,
            'ignored-key',
            'https://cap.url/',
            '{"datadomeCookie":"dd=1"}',
            'UA',
            'c=1',
            self::proxy()
        );

        self::assertSame(
            [
                'type' => 'CustomTask',
                'proxyType' => 'http',
                'proxyAddress' => '1.2.3.4',
                'proxyPort' => 8080,
                'proxyLogin' => 'user',
                'proxyPassword' => 'pass',
                'websiteURL' => self::URL,
                'userAgent' => 'UA',
                'class' => 'DataDome',
                'metadata' => [
                    'datadomeCookie' => 'dd=1',
                    'captchaUrl' => 'https://cap.url/',
                ],
            ],
            $this->taskPayload($task)
        );
    }

    public function testImpervaTaskStripsWebsiteKeyAndCookies(): void
    {
        $task = new ImpervaTask(self::URL, self::KEY, '{"m":2}', 'UA', 'c=2');
        $payload = $this->taskPayload($task);

        self::assertSame('CustomTask', $payload['type']);
        self::assertSame('Imperva', $payload['class']);
        self::assertSame(['m' => 2], $payload['metadata']);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertArrayNotHasKey('cookies', $payload);
    }

    public function testTspdTaskStripsWebsiteKeyAndCookies(): void
    {
        $task = new TSPDTask(self::URL, self::KEY, '{"m":5}', 'UA', 'c=5');
        $payload = $this->taskPayload($task);

        self::assertSame('CustomTask', $payload['type']);
        self::assertSame('tspd', $payload['class']);
        self::assertSame(['m' => 5], $payload['metadata']);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertArrayNotHasKey('cookies', $payload);
    }

    public function testAlibabaTaskStripsWebsiteKeyAndCookies(): void
    {
        $task = new AlibabaTask(self::URL, self::KEY, '{"m":7}', 'UA', 'c=7');
        $payload = $this->taskPayload($task);

        self::assertSame('CustomTask', $payload['type']);
        self::assertSame(['m' => 7], $payload['metadata']);
        self::assertArrayNotHasKey('websiteKey', $payload);
        self::assertArrayNotHasKey('cookies', $payload);
    }

    public function testAlibabaTaskDeclaresCustomTaskClassAlibaba(): void
    {
        $payload = $this->taskPayload(new AlibabaTask(self::URL, self::KEY));

        // Per https://docs.capmonster.cloud/ru/docs/captchas/alibaba-task/ the CustomTask
        // discriminator "class":"alibaba" is required.
        self::assertArrayHasKey('class', $payload);
        self::assertSame('alibaba', $payload['class']);
        self::assertSame('CustomTask', $payload['type']);
    }

    public function testAltchaTaskKeepsWebsiteKeyButStripsCookies(): void
    {
        $task = new AltchaTask(self::URL, self::KEY, '{"m":3}', 'UA', 'c=3');
        $payload = $this->taskPayload($task);

        self::assertSame('CustomTask', $payload['type']);
        self::assertSame('altcha', $payload['class']);
        self::assertSame(self::KEY, $payload['websiteKey']);
        self::assertArrayNotHasKey('cookies', $payload);
    }

    public function testBasiliskTenDiCastleHuntCustomTaskClasses(): void
    {
        self::assertSame('Basilisk', $this->taskPayload(new BasiliskTask(self::URL, self::KEY))['class']);
        self::assertSame('TenDI', $this->taskPayload(new TenDITask(self::URL, self::KEY))['class']);
        self::assertSame('Castle', $this->taskPayload(new CastleTask(self::URL, self::KEY))['class']);
        self::assertSame('HUNT', $this->taskPayload(new HuntTask(self::URL, self::KEY))['class']);
    }

    public function testCustomTaskMetadataDefaultsToEmptyObject(): void
    {
        $payload = $this->taskPayload(new BasiliskTask(self::URL, self::KEY));

        self::assertSame('CustomTask', $payload['type']);
        self::assertSame([], $payload['metadata']);
    }

    // ------------------------------------------------------------------ remaining task types

    public function testBinanceTaskSerializesValidateIdInCamelCase(): void
    {
        $task = new BinanceTask(self::URL, self::KEY, 'VALID', 'UA');
        $payload = $this->taskPayload($task);

        self::assertArrayNotHasKey('validate_id', $payload);
        self::assertSame(
            [
                'type' => 'BinanceTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'validateId' => 'VALID',
            ],
            $payload
        );
    }

    public function testAmazonTaskFullPayload(): void
    {
        $task = new AmazonTask(self::URL, self::KEY, 'chs', 'cps', 'ctx', 'iv', true, 'UA', 'c=1');

        self::assertSame(
            [
                'type' => 'AmazonTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'userAgent' => 'UA',
                'cookies' => 'c=1',
                'challengeScript' => 'chs',
                'captchaScript' => 'cps',
                'context' => 'ctx',
                'iv' => 'iv',
                'cookieSolution' => true,
            ],
            $this->taskPayload($task)
        );
    }

    public function testProsopoTaskMinimalPayload(): void
    {
        self::assertSame(
            [
                'type' => 'ProsopoTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
            ],
            $this->taskPayload(new ProsopoTask(self::URL, self::KEY))
        );
    }

    public function testYidunTaskFullPayload(): void
    {
        $task = new YidunTask(self::URL, self::KEY, 'lib', 'sub', 'chal', 'hcg', 'hct');

        self::assertSame(
            [
                'type' => 'YidunTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'yidunGetLib' => 'lib',
                'yidunApiServerSubdomain' => 'sub',
                'challenge' => 'chal',
                'hcg' => 'hcg',
                'hct' => 'hct',
            ],
            $this->taskPayload($task)
        );
    }

    public function testMtCaptchaTaskFullPayload(): void
    {
        $task = new MTCaptchaTask(self::URL, self::KEY, 'act', true);

        self::assertSame(
            [
                'type' => 'MTCaptchaTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
                'pageAction' => 'act',
                'isInvisible' => true,
            ],
            $this->taskPayload($task)
        );
    }

    public function testOptionalNullFieldsAreOmittedFromPayload(): void
    {
        $payload = $this->taskPayload(new TurnstileTask(self::URL, self::KEY));

        self::assertSame(
            [
                'type' => 'TurnstileTask',
                'websiteURL' => self::URL,
                'websiteKey' => self::KEY,
            ],
            $payload
        );
    }
}
