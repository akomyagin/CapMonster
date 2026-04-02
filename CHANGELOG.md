# Changelog

## Unreleased

### Added
- Expanded `TypeTask` to include the current captcha/task set from CapMonster docs.
- Added new task DTOs for extended captcha providers and Cloudflare challenge flows.
- Added `RawSolution` for providers with custom/variable solution payload.
- Added `CapMonsterClient::getActualUserAgent()` helper.
- Added typed exception hierarchy: `AuthException`, `BalanceException`, `ProxyException`, `TaskValidationException`, `RuntimeCapMonsterException`.
- Added static factory `ProxySetting::create(...)`.

### Changed
- Expanded `ErrorType` with missing API error codes (`ERROR_PROXY_MISSING`, `ERROR_PROXY_NOT_AUTHORISED`, `ERROR_PROXY_READ_TIMEOUT`, `ERROR_TASK_ABSENT`, `ERROR_WRONG_USERAGENT`).
- `ApiClient` now builds typed exceptions through `ExceptionFactory`.
- Default timeout configuration now covers all `TypeTask` enum values.
- Updated resolver and transformer tests to cover new task/error matrix.

### Tests
- Extended unit coverage for error mapping, request payloads and solution resolver.
- Added integration check for configured `callbackUrl` contract.
