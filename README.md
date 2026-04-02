# CapMonster PHP Client

PHP SDK for [CapMonster Cloud API](https://docs.capmonster.cloud/ru/docs/getting-start).

## Covered API methods

- `createTask`
- `getTaskResult`
- `getBalance`
- Actual User-Agent helper: `getActualUserAgent()`

## Implemented task model coverage

The client supports base and extended task types from CapMonster docs:

- reCAPTCHA: v2, v3, v2 Enterprise, v3 Enterprise
- Cloudflare: Turnstile, Challenge, Waiting Room
- Image tasks: ImageToText, ComplexImage
- Other providers: GeeTest, hCaptcha, FunCaptcha, DataDome, Basilisk, TenDI, Amazon, Binance, Imperva, Prosopo, Yidun, MTCaptcha, Altcha, Castle, TSPD, Hunt, Alibaba

## Notes

- Requests are sent as `JSON POST` to `https://api.capmonster.cloud`.
- Polling limit for `getTaskResult` is enforced (120 attempts).
- Default polling interval for all task types is conservative and API-safe.
- `callbackUrl` is supported through `CapMonsterConfiguration`.

## Error handling

All API error codes are mapped to `ErrorType` values.

The client raises typed exceptions by category:

- `AuthException`
- `BalanceException`
- `ProxyException`
- `TaskValidationException`
- `RuntimeCapMonsterException`

All exceptions extend `CapMonsterException`.

## Running tests

```bash
composer test
composer test:unit
composer test:integration
composer test:functional
```
