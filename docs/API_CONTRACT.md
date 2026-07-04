# CapMonster Cloud — API Contract (doc-verified)

This file is the **source of truth** for the wire contract of every task type the library
supports. It was derived **independently from the current implementation** by reading the
official documentation MDX sources (which are NOT Cloudflare-protected):

- Captcha pages: `https://raw.githubusercontent.com/ZennoLab/capmonstercloud-docs/dev/docs/captchas/<name>.mdx`
- ComplexImage modules: `.../docs/captchas/compleximage/<category>/<module>.mdx`
- API methods: `.../docs/api/methods/<method>.mdx`

All JSON below is quoted from the literal `json` code blocks in those docs. Where the docs are
ambiguous or self-contradictory it is flagged explicitly rather than guessed.

## API methods

| Method | Endpoint | Request | Response |
|---|---|---|---|
| createTask | `POST {baseUrl}/createTask` | `{clientKey, task:{...}, callbackUrl?}` | `{errorId, taskId}` or `{errorId, errorCode, errorDescription, taskId:0}` |
| getTaskResult | `POST {baseUrl}/getTaskResult` | `{clientKey, taskId}` | `{errorId, status:"processing"}` or `{errorId, status:"ready", solution:{...}}` |
| getBalance | `POST {baseUrl}/getBalance` | `{clientKey}` | `{errorId, balance: 345.678}` |
| getUserAgent | `GET https://capmonster.cloud/api/useragent/actual` | — | plain-text UA string |

`baseUrl` = `https://api.capmonster.cloud`. `status` is `processing` | `ready`. On error `errorId>0`
and `errorCode` is one of the `ERROR_*` strings.

Note: the `getBalance` doc example shows only `{errorId, balance}` — there is no `errorCode` key on
the success path, but the client tolerates its absence (see `AbstractResponse`).

## `type` value convention

- **Own-name types** (`type` == the documented task name): ImageToText, RecaptchaV2 (see alias
  note), RecaptchaV3, RecaptchaV2/V3 Enterprise, FunCaptcha, HCaptcha, GeeTest, Turnstile (all
  three variants), ComplexImage, Amazon, Binance, Imperva*, Prosopo, Yidun, MTCaptcha.
- **`type: CustomTask` + `class` discriminator**: DataDome (`DataDome`), Imperva/Incapsula
  (`Imperva`), Basilisk (`Basilisk`), TenDI (`TenDI`), Castle (`Castle`), Hunt (`HUNT`),
  Alibaba (`alibaba`), Altcha (`altcha`), TSPD (`tspd`). ComplexImage also carries a `class`
  (`recognition` for image modules, `recaptcha` for the reCAPTCHA-click variant) but keeps its
  own `type: ComplexImageTask`.
- **Turnstile family**: all three (plain / challenge / waiting-room) send `type: TurnstileTask`.
  They are distinguished by the `cloudflareTaskType` field, NOT by CustomTask:
  - plain: no `cloudflareTaskType`
  - challenge: `cloudflareTaskType` = `token` | `cf_clearance`
  - waiting-room: `cloudflareTaskType` = `wait_room`
- **RecaptchaV2 alias**: the wire type is `RecaptchaV2Task` / `RecaptchaV2TaskProxyless`, but the
  library's PHP class is `NoCaptchaTask` (enum values `NoCaptchaTask` / `NoCaptchaTaskProxyless`),
  rewritten to the `RecaptchaV2*` wire name in `RequestTransformer::TASK_TYPE_ALIASES`.

---

## Task-by-task contract

Legend: **R** = required, **o** = optional. `metadata.*` = nested inside a `metadata` object.

### ImageToTextTask  —  `type: ImageToTextTask`
Fields: `body`(R), `capMonsterModule`(o), `recognizingThreshold`(o int), `case`(o bool),
`numeric`(o int), `math`(o bool). No websiteURL/websiteKey.
Solution: `{ "text": "answer" }` → **TextSolution**.

### RecaptchaV2 (NoCaptchaTask)  —  `type: RecaptchaV2Task` / `RecaptchaV2TaskProxyless`
Fields: `websiteURL`(R), `websiteKey`(R), `recaptchaDataSValue`(o), `isInvisible`(o bool),
`userAgent`(o), `cookies`(o), proxy(o). Proxy presence selects proxied vs proxyless type.
Solution: `{ "gRecaptchaResponse": "..." }` (may also carry `userAgent`, `cookies`) → **ReCaptchaSolution**.

### RecaptchaV3TaskProxyless  —  `type: RecaptchaV3TaskProxyless`
Fields: `websiteURL`(R), `websiteKey`(R), `minScore`(o double), `pageAction`(o),
`isEnterprise`(o bool — **not modelled in src**, see discrepancies).
Solution: `{ "gRecaptchaResponse": "..." }` → **ReCaptchaSolution**.

### RecaptchaV2EnterpriseTask  —  `type: RecaptchaV2EnterpriseTask` / `...Proxyless`
Fields: `websiteURL`(R), `websiteKey`(R), `pageAction`(o), `enterprisePayload`(o), `apiDomain`(o),
`userAgent`(o), `cookies`(o), proxy(o).
Doc shows `enterprisePayload` as an **object** `{"s":"..."}` — src models it as a string (see discrepancies).
Solution: `{ "gRecaptchaResponse": "..." }` → **ReCaptchaSolution**.

### RecaptchaV3EnterpriseTask  —  `type: RecaptchaV3EnterpriseTask` (`...Proxyless` in doc examples)
Fields: `websiteURL`(R), `websiteKey`(R), `minScore`(o double), `pageAction`(o). (src also accepts
`enterprisePayload`/`apiDomain`, harmlessly omitted when empty.)
Solution: `{ "gRecaptchaResponse": "..." }` (optionally `userAgent`) → **ReCaptchaSolution**.

### FunCaptchaTask  —  `type: FunCaptchaTask` / `FunCaptchaTaskProxyless`
Fields: `websiteURL`(R), `websitePublicKey`(R), `data`(o), `funcaptchaApiJSSubdomain`(o),
`userAgent`(o), `cookies`(o), proxy(o). NOTE: the wire field is `websitePublicKey`, not `websiteKey`.
Solution: `{ "token": "...", "userAgent": "..." }` → **TokenSolution**.

### HCaptchaTask  —  `type: HCaptchaTask` / `HCaptchaTaskProxyless`
Fields: `websiteURL`(R), `websiteKey`(R), `isInvisible`(o bool), `data`(o), `userAgent`(o),
`cookies`(o), `fallbackToActualUA`(o bool), proxy(o).
Solution: `{ "gRecaptchaResponse": "...", "respKey": "...", "userAgent": "..." }` → **HCaptchaSolution**
(all three keys present in the documented ready response).

### GeeTestTask  —  `type: GeeTestTask` / `GeeTestTaskProxyless`
Fields: `websiteURL`(R), `gt`(R), `challenge`(R for V3), `version`(R int: 3 or 4),
`geetestApiServerSubdomain`(o), `geetestGetLib`(o), `initParameters`(o object, V4, holds `riskType`),
`userAgent`(o), proxy(o). **There is NO `websiteKey` in the GeeTest payload** — the domain key
travels only as `gt`.
Solution V3: `{ "challenge", "validate", "seccode" }`.
Solution V4: `{ "captcha_id", "lot_number", "pass_token", "gen_time", "captcha_output" }`
(a V4 variant also returns `encryptedData`, usually empty). → **GeeTestSolution** (all fields nullable).

### TurnstileTask (plain)  —  `type: TurnstileTask`
Fields: `websiteURL`(R), `websiteKey`(R), `userAgent`(o), `pageAction`(o), `data`(o), proxy(o).
Solution: `{ "userAgent": "...", "token": "..." }` → **TokenSolution**.

### TurnstileChallengeTask  —  `type: TurnstileTask` (+ `cloudflareTaskType`)
Fields: `websiteURL`(R), `websiteKey`(R), `cloudflareTaskType`(R: `token`|`cf_clearance`),
`pageAction`(o), `userAgent`(o), `data`(o), `pageData`(o), `htmlPageBase64`(o — used by the
alternate challenge variant), proxy(o).
Solution: `token` variant → `{ "userAgent", "token" }`; `cf_clearance` variant → `{ "cf_clearance" }`
→ **TokenSolution**.

### TurnstileWaitingRoomTask  —  `type: TurnstileTask` (+ `cloudflareTaskType: wait_room`)
Fields: `websiteURL`(R), `websiteKey`(R), `htmlPageBase64`(R), `userAgent`(o), proxy(o).
Solution: `{ "cf_clearance": "..." }` → **TokenSolution**.

### ComplexImageTask  —  `type: ComplexImageTask`, `class: recognition`
Fields: `imagesBase64`(R array), `metadata.Task`(R — e.g. `baidu`, `dli`, `bls_3x3`, `shein`,
`bills_audio`), plus per-module `metadata.*` (e.g. `TaskArgument`, `PayloadType`). `websiteUrl`/
`userAgent` optional for some modules.
Solution shape varies by module → **RawSolution**:
- NumericArray: `{ "answer":[297], "metadata":{"AnswerType":"NumericArray"} }`
- Text: `{ "answer":"1", "metadata":{"AnswerType":"Text"} }`
- Grid: `{ "answer":[true,false,...], "metadata":{"AnswerType":"Grid"} }`
- Coordinate: `{ "answer":[{"X":..,"Y":..}], "metadata":{"AnswerType":"Coordinate"} }`

### AmazonTask  —  `type: AmazonTask` (own name even without proxy)
Fields (Puzzle): `websiteURL`(R), `websiteKey`(R), `captchaScript`(R), `challengeScript`(o),
`context`(o), `iv`(o), `cookieSolution`(o bool), `userAgent`(o), proxy(o). (WAF-token variant
requires `challengeScript`/`context`/`iv`.) src makes all optional except URL/key which is a
superset-compatible relaxation.
Solution: `{ "cookies": { "aws-waf-token": "..." }, "userAgent": "..." }` → **RawSolution**.

### BinanceTask  —  `type: BinanceTask`
Fields: `websiteURL`(R), `websiteKey`(R, e.g. "login"), `validateId`(R), `userAgent`(o), proxy(o).
Solution: `{ "token": "captcha#...", "userAgent": "..." }` → **RawSolution** (token via `getPayload()['token']`).

### ImpervaTask (Incapsula)  —  `type: CustomTask`, `class: Imperva`
Fields: `websiteURL`(R), `metadata.incapsulaScriptUrl`(R), `metadata.incapsulaCookies`(R),
`metadata.reese84UrlEndpoint`(o), `userAgent`(o), proxy(R). No `websiteKey`, no `cookies` on the wire
(src strips both). Metadata passed as a JSON string to the DTO.
Solution: `{ "domains": { "<url>": { "cookies": { "___utmvc": "..." } } } }` → **RawSolution**.

### ProsopoTask  —  `type: ProsopoTask`
Fields: `websiteURL`(R), `websiteKey`(R), proxy(o).
Solution: `{ "token": "0x..." }` → **RawSolution**.

### YidunTask  —  `type: YidunTask`
Fields: `websiteURL`(R), `websiteKey`(R), `userAgent`(o), `yidunGetLib`(o), `yidunApiServerSubdomain`(o),
`challenge`(o), `hcg`(o), **`hct`(o INTEGER — doc example `1751469954806`)**, proxy(o).
Solution: `{ "token": "..." }` → **RawSolution**.

### MTCaptchaTask  —  `type: MTCaptchaTask`
Fields: `websiteURL`(R), `websiteKey`(R, `MTPublic-...`), `pageAction`(o), `isInvisible`(o bool),
`userAgent`(o), proxy(o).
Solution: `{ "token": "v1(...)" }` → **RawSolution**.

### DataDomeTask  —  `type: CustomTask`, `class: DataDome`
Fields: `websiteURL`(R), `metadata.captchaUrl`(R), `metadata.datadomeCookie`(R),
`metadata.datadomeVersion`(o), `userAgent`(o), proxy(R). No `websiteKey`/`cookies` on the wire
(src strips both; `captchaUrl` is merged into `metadata`).
Solution: `{ "domains": { "<host>": { "cookies": { "datadome": "..." }, "localStorage": null } },
"url":null, "fingerprint":null, "headers":null, "data":null }` → **RawSolution**.

### BasiliskTask  —  `type: CustomTask`, `class: Basilisk`
Fields: `websiteURL`(R), `websiteKey`(R), `userAgent`(o), proxy(o).
Solution: `{ "data": { "captcha_response": "..." }, "headers": { "User-Agent": "..." } }` → **RawSolution**.

### TenDITask  —  `type: CustomTask`, `class: TenDI`
Fields: `websiteURL`(R), `websiteKey`(R), `metadata.captchaUrl`(o), `userAgent`(o), proxy(o).
Solution: `{ "data": { "randstr": "...", "ticket": "..." }, "headers": {...} }` → **RawSolution**.

### CastleTask  —  `type: CustomTask`, `class: Castle`
Fields: `websiteURL`(R), `websiteKey`(R), `metadata.wUrl`(R), `metadata.swUrl`(R),
`metadata.count`(o int), `userAgent`(o), proxy(o).
Solution: `{ "data": { "tokens": [...] }, "domains": { "<host>": { "cookies": {...} } } }` → **RawSolution**.
**Ambiguity**: the Castle JSON example writes the discriminator as `"Class": "Castle"` (capital C)
while the ParamItem spec and every other CustomTask uses lowercase `"class"`. Treated as a doc typo;
the library sends lowercase `class` (consistent with the documented CustomTask convention).

### HuntTask  —  `type: CustomTask`, `class: HUNT`
Fields: `websiteURL`(R), `metadata.apiGetLib`(R), `metadata.data`(o), `userAgent`(o), proxy(R).
Solution: `{ "data": { "token": "..." } }` → **RawSolution**.

### AlibabaTask  —  `type: CustomTask`, `class: alibaba`
Fields: `websiteURL`(R), `metadata.sceneId`(R), `metadata.prefix`(R), plus optional
`metadata.{userId,userUserId,verifyType,region,UserCertifyId,apiGetLib}`, `userAgent`(o), proxy(o).
No `websiteKey`/`cookies` on the wire (src strips both).
Solution: `{ "data": { "tokens": "{...json string...}" } }` → **RawSolution**.

### AltchaTask  —  `type: CustomTask`, `class: altcha`
Fields: `websiteURL`(R), `websiteKey`(R, may be `""`), `metadata.challenge`(R),
`metadata.iterations`(R), `metadata.salt`(R), `metadata.signature`(R), `userAgent`(o), proxy(o).
`cookies` is stripped by src. `websiteKey` is kept.
Solution: `{ "data": { "token": "...", "number": 44619 } }` → **RawSolution**.

### TSPDTask  —  `type: CustomTask`, `class: tspd`
Fields: `websiteURL`(R), `metadata.tspdCookie`(R), `metadata.htmlPageBase64`(R), `userAgent`(o),
proxy(R). No `websiteKey`/`cookies` on the wire (src strips both).
Solution: `{ "Domains": { "<host>": { "Cookies": { ... } } } }` → **RawSolution**.

---

## Discrepancies found vs. the current `src/` implementation

1. **YidunTask.hct — WRONG TYPE (fixed).** Docs declare `hct` as `integer`
   (example `1751469954806`); src modelled it as `?string`, so a numeric timestamp would have
   serialized as a quoted string (`"hct":"..."`) instead of a JSON number. Changed to `?int` with
   `#[Serializer\Type('integer')]`. Test updated to assert the integer wire value.

2. **RecaptchaV3TaskProxyless.isEnterprise — MISSING optional field (not fixed).** The doc lists an
   optional `isEnterprise` boolean; the src DTO has no such parameter. Left as-is because it is
   optional and rarely used; documented here as a known gap rather than silently added.

3. **enterprisePayload object vs string — AMBIGUOUS (not changed).** The V2/V3 Enterprise docs show
   `enterprisePayload` as a JSON **object** (`{"s":"..."}`); src types it as a string. The CapMonster
   API is widely reported to accept the value either way, and changing the DTO type is a breaking
   design change. Flagged as an unresolved discrepancy; the library keeps the string form. Callers
   who need the object form can pass a JSON-encoded string.

4. **DataDome/TSPD/Hunt required metadata not first-class.** `datadomeCookie` (DataDome),
   `tspdCookie`/`htmlPageBase64` (TSPD), `apiGetLib` (Hunt) are documented as required, but src
   accepts them through the generic `metadata` JSON-string parameter and does not enforce presence.
   This is a deliberate design (generic metadata passthrough), not a wire-shape bug.

5. **ComplexImage `class: recaptcha` variant unsupported.** The recaptcha-click ComplexImage variant
   uses `class: recaptcha` + `metadata.{Grid,TaskDefinition}`; src hardcodes `class: recognition`.
   Out of the 30-type scope; noted for completeness.

## Confidence

HIGH for the create-task wire shape and solution shape of: ImageToText, RecaptchaV2/V3,
RecaptchaV2/V3 Enterprise, FunCaptcha, HCaptcha, GeeTest (V3+V4), all three Turnstile variants,
ComplexImage, Amazon, Binance, MTCaptcha, Yidun, Prosopo, DataDome, Imperva, Basilisk, TenDI, Hunt,
Alibaba, Altcha, TSPD — all cross-checked against literal JSON in the doc MDX.

MEDIUM for Castle (no create-task JSON in the primary tab; `Class`/`class` casing inconsistency) and
for the `enterprisePayload` object-vs-string question (docs and real-world usage disagree).
