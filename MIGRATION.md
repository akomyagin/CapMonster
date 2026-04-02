# Migration Notes

## Exception categories

The client now throws specialized exceptions extending `CapMonsterException`.

If your code catches only `CapMonsterException`, it remains compatible.
If you need granular handling, catch:

- `AuthException`
- `BalanceException`
- `ProxyException`
- `TaskValidationException`
- `RuntimeCapMonsterException`

## Task coverage

`TypeTask` includes additional task types from the current CapMonster docs.

Existing task classes and aliases remain supported, including:
- `NoCaptchaTask` -> `RecaptchaV2Task`
- `NoCaptchaTaskProxyless` -> `RecaptchaV2TaskProxyless`

## Timeouts

Default timeout settings are now generated for all `TypeTask` values.
Custom timeouts still override defaults per task type.

## New helper API

`CapMonsterClientInterface` now includes:

- `getActualUserAgent(): string`

Implementations using the interface should add this method.
