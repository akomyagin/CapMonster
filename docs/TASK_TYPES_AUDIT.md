# Аудит типов задач: документация CapMonster vs PHP-клиент

Источник: [каталог капч](https://docs.capmonster.cloud/ru/docs/captchas/) и страницы API. Проход: 2026-04-02.

**OK** — имя `type` в JSON совпадает с докой. **CustomTask** — в доке `type: CustomTask` и поле `class`. **GAP** — DTO не отражает обязательные поля из доки. **FIX** — JMS `VirtualProperty` / `Exclude` на классах задач, плоский proxy в `AbstractTask`, точечная доводка в `RequestTransformer::finalizeCreateTaskPayload()` (удаление `websiteKey`/`cookies` где нужно, `FunCaptcha` → `websitePublicKey`).

## Результаты по типам

- **reCAPTCHA v2**: дока `RecaptchaV2Task`; PHP `NoCaptchaTask` → алиас в `RequestTransformer`. OK.
- **FunCaptcha**: дока `FunCaptchaTask`, ключ **`websitePublicKey`**; FIX: в `RequestTransformer::finalizeCreateTaskPayload()` выставляется `websitePublicKey`, `websiteKey` убирается (JMS даёт snake_case для виртуальных полей с нестандартными именами методов).
- **AmazonTask**: дока несколько вариантов (`captchaScript`, `challengeScript`, `context`, `iv`, `cookieSolution`). GAP: в PHP только `challengeScript`.
- **BinanceTask**: дока обязателен **`validateId`**. GAP: в PHP только `metadata` строка.
- **ProsopoTask**, **YidunTask**, **MTCaptchaTask**: имена типов OK; GAP: расширенные поля из доки (Yidun/MTCaptcha) не вынесены в свойства.
- **DataDome**: дока `CustomTask` + `class: DataDome`; FIX: сбор `metadata` и `captchaUrl`.
- **Imperva**: дока `CustomTask` + `Imperva`; FIX.
- **Basilisk, TenDI, Castle, Hunt**: дока `CustomTask` + соответствующий `class`; FIX + JSON `metadata` → объект.
- **Alibaba**: дока `CustomTask` + `class: alibaba`; FIX.
- **Altcha**: дока `CustomTask` + `class: altcha`; FIX.
- **TSPD**: дока `CustomTask` + `class: tspd`; FIX.

## Общие исправления в коде

1. **`websiteURL`**: `SerializedName` на поле в `AbstractTask`.
2. **`taskId`**: не сериализуется в `task` (`Exclude`).
3. **Прокси**: разворачивается в плоские поля корня `task` из `ProxySetting`.
4. Реестр соответствия строки типа и PHP-класса: `Serializer\TaskDiscriminatorRegistry::TYPE_TO_CLASS` (вместо JMS `#[Discriminator]` на `AbstractTask` из‑за proxyless/CustomTask).
5. Остальные GAP (Amazon/Binance/Yidun/MTCaptcha) зафиксированы выше; правки DTO — отдельная задача.

## Ссылки на документацию

- [createTask](https://docs.capmonster.cloud/ru/docs/api/methods/create-task/)
- [reCAPTCHA v2](https://docs.capmonster.cloud/ru/docs/captchas/no-captcha-task/)
- [DataDome](https://docs.capmonster.cloud/ru/docs/captchas/datadome/) · [Imperva](https://docs.capmonster.cloud/ru/docs/captchas/incapsula/) · [Basilisk](https://docs.capmonster.cloud/ru/docs/captchas/Basilisk-task/) · [TenDI](https://docs.capmonster.cloud/ru/docs/captchas/tendi/) · [Alibaba](https://docs.capmonster.cloud/ru/docs/captchas/alibaba-task/) · [Castle](https://docs.capmonster.cloud/ru/docs/captchas/castle-task/) · [Hunt](https://docs.capmonster.cloud/ru/docs/captchas/hunt-task/) · [Altcha](https://docs.capmonster.cloud/ru/docs/captchas/altcha-task/) · [TSPD](https://docs.capmonster.cloud/ru/docs/captchas/tspd-task/)
- [Binance](https://docs.capmonster.cloud/ru/docs/captchas/binance) · [Amazon](https://docs.capmonster.cloud/ru/docs/captchas/amazon-task/) · [FunCaptcha](https://docs.capmonster.cloud/ru/docs/captchas/funcaptcha-task/) · [Prosopo](https://docs.capmonster.cloud/ru/docs/captchas/prosopo-task/) · [Yidun](https://docs.capmonster.cloud/ru/docs/captchas/yidun-task/) · [MTCaptcha](https://docs.capmonster.cloud/ru/docs/captchas/mtcaptcha-task/)
