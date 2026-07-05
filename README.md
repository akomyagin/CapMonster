# CapMonster Cloud PHP Client

A PHP 8.2+ client for the [CapMonster Cloud](https://capmonster.cloud/) captcha-solving API. Bring your own [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client, build a task DTO, get a solution back.

## Requirements

- PHP 8.2+
- A [PSR-18](https://www.php-fig.org/psr/psr-18/) HTTP client implementation (e.g. `symfony/http-client`, `guzzlehttp/guzzle` with `php-http/guzzle7-adapter`, etc.)

## Installation

```bash
composer require cap_monster/client
```

## Usage

```php
use CapMonsterClient\CapMonsterClient;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Task\NoCaptchaTask;

$configuration = new CapMonsterConfiguration('YOUR_CAPMONSTER_CLIENT_KEY');
$client = new CapMonsterClient($yourPsr18HttpClient, $configuration);

$task = new NoCaptchaTask(
    websiteUrl: 'https://example.com/page-with-captcha',
    websiteKey: 'site-key-from-the-page'
);

$solution = $client->runTask($task); // blocks, polling getTaskResult until ready or timeout
echo $solution->getGRecaptchaResponse();

$balance = $client->getBalance();
```

`runTask()` handles `createTask` + polling `getTaskResult` for you, and throws `CapMonsterClient\Common\Exception\CapMonsterException` (wrapping a `CapMonsterClient\Enum\ErrorType`) on any API or timeout error.

## Supported captcha types

`ImageToTextTask`, `NoCaptchaTask`/`NoCaptchaTaskProxyless` (reCAPTCHA v2), `RecaptchaV2EnterpriseTask`, `RecaptchaV3TaskProxyless`, `RecaptchaV3EnterpriseTask`, `FunCaptchaTask`/`FunCaptchaTaskProxyless`, `HCaptchaTask`/`HCaptchaTaskProxyless`, `GeeTestTask` (v3 & v4), `TurnstileTask`, `TurnstileChallengeTask`, `TurnstileWaitingRoomTask`, `ComplexImageTask`, `DataDomeTask`, `ImpervaTask`, `BasiliskTask`, `TenDITask`, `AmazonTask`, `BinanceTask`, `ProsopoTask`, `YidunTask`, `MTCaptchaTask`, `AltchaTask`, `CastleTask`, `TSPDTask`, `HuntTask`, `AlibabaTask`.

Each task's exact request/response shape is documented in the source repository's `docs/API_CONTRACT.md` (not shipped in the installed package — see the [GitHub repo](https://github.com/akomyagin/CapMonster)).

> **Note:** per CapMonster Cloud support (July 2026) and confirmed against the live API, hCaptcha is currently **not** being solved by the service — `createTask` with an `HCaptchaTask`/`HCaptchaTaskProxyless` payload is immediately rejected with `ERROR_TASK_NOT_SUPPORTED` (no balance charged), despite the task being fully implemented here per the documented wire format. Check with CapMonster support for current availability before relying on it.

## Configuration

`CapMonsterConfiguration` accepts an optional array of overrides (base URL, per-task polling timeouts, HTTP headers, callback URL, max poll attempts) — see `CapMonsterConfiguration::__construct()` for the full option set.

## Verified against the live API

`ImageToTextTask`, `NoCaptchaTask` (reCAPTCHA v2), and `TurnstileTask` have each been run end-to-end against the real `api.capmonster.cloud` (real account, real balance deduction) and correctly solved. `HCaptchaTask` was confirmed rejected by the live API as described above.

## License

MIT
