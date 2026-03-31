<?php

declare(strict_types=1);

namespace CapMonsterClient\Enum;

enum ErrorType: string implements EnumDescriptionInterface
{
    case INVALID_KEY = 'ERROR_KEY_DOES_NOT_EXIST';

    case NO_FUNDS = 'ERROR_ZERO_BALANCE';

    case BIG_IMAGE_SIZE = 'ERROR_TOO_BIG_CAPTCHA_FILESIZE';

    case ZERO_IMAGE_SIZE = 'ERROR_ZERO_CAPTCHA_FILESIZE';

    case CAPTCHA_ID_IS_NOT_FOUND = 'ERROR_NO_SUCH_CAPCHA_ID';

    case CAPTCHA_ID_IS_NOT_FOUND_2 = 'WRONG_CAPTCHA_ID';

    case CAPTCHA_UNSOLVABLE = 'ERROR_CAPTCHA_UNSOLVABLE';

    case CAPTCHA_IS_NOT_READY = 'CAPTCHA_NOT_READY';

    case REQUEST_IS_NOT_ALLOWED_FROM_YOUR_IP = 'ERROR_IP_NOT_ALLOWED';

    case IP_BANNED = 'ERROR_IP_BANNED';

    case INCORRECT_METHOD = 'ERROR_NO_SUCH_METHOD';

    case REQUEST_LIMIT_EXCEEDED = 'ERROR_TOO_MUCH_REQUESTS';

    case THE_DOMAIN_IS_NOT_ALLOWED = 'ERROR_DOMAIN_NOT_ALLOWED';

    case THE_TOKEN_IS_EXPIRED = 'ERROR_TOKEN_EXPIRED';

    case NO_FREE_SERVERS = 'ERROR_NO_SLOT_AVAILABLE';

    case INVALID_RECAPTCHA_SITEKEY = 'ERROR_RECAPTCHA_INVALID_SITEKEY';

    case INVALID_RECAPTCHA_DOMAIN = 'ERROR_RECAPTCHA_INVALID_DOMAIN';

    case RECAPTCHA_TIMEOUT = 'ERROR_RECAPTCHA_TIMEOUT';

    case YOUR_IP_IS_BLOCKED = 'ERROR_IP_BLOCKED';

    case FAILED_TO_CONNECT_PROXY = 'ERROR_PROXY_CONNECT_REFUSED';

    case THE_PROXY_IP_IS_BANNED = 'ERROR_PROXY_BANNED';

    case INCORRECT_TASK_TYPE = 'ERROR_TASK_NOT_SUPPORTED';

    case INVALID_ARGUMENT_EXCEPTION = 'ERROR_ARGUMENT_INVALID_EXCEPTION';

    case SEND_MESSAGE_ERROR = 'ERROR_SEND_TO_API';

    case TYPE_TASK_RESOLVE_EXCEPTION = 'ERROR_TYPE_TASK';

    case TIMEOUT_EXPIRED = 'TIMEOUT_EXPIRED';

    case RESPONSE_ERROR = 'RESPONSE_ERROR';

    case RESPONSE_CODE_ERROR = 'RESPONSE_CODE_ERROR';

    case UNKNOWN_ERROR = 'RESPONSE_UNKNOWN_ERROR';

    public function description(): string
    {
        return match ($this) {
            ErrorType::INVALID_KEY => 'API-ключ не существует в системе или имеет неверный формат. Проверьте корректность его написания.',
            ErrorType::NO_FUNDS => 'Баланс учетной записи равен нулю. Пополните баланс своего кабинета, чтобы продолжить распознавание.',
            ErrorType::BIG_IMAGE_SIZE => 'Размер капчи которую вы загружаете более 500,000 байт.',
            ErrorType::ZERO_IMAGE_SIZE => 'Размер капчи которую вы загружаете менее 100 байт.',
            ErrorType::CAPTCHA_ID_IS_NOT_FOUND, ErrorType::CAPTCHA_ID_IS_NOT_FOUND_2 => 'Капча с таким ID не была найдена в системе. Убедитесь что вы запрашиваете состояние капчи в течение 5 минут после загрузки.',
            ErrorType::CAPTCHA_UNSOLVABLE => 'Данный тип капч не поддерживается сервисом или картинка не содержит ответа, то есть является шумом. Возможно она является поврежденной или неправильно отрисованной.',
            ErrorType::CAPTCHA_IS_NOT_READY => 'Решение данной капчи еще не готово.',
            ErrorType::REQUEST_IS_NOT_ALLOWED_FROM_YOUR_IP => 'Запрос с этого IP адреса с текущим ключом отклонен. Откройте раздел настроек в личном кабинете и добавьте свой IP в список доверенных.',
            ErrorType::IP_BANNED => 'Вы превысили лимит запросов с неправильным API-ключом, проверьте правильность ключа в панели управления и через некоторое время повторите попытку.',
            ErrorType::INCORRECT_METHOD => 'Неправильно указан тип капчи (значение параметра «type»).',
            ErrorType::REQUEST_LIMIT_EXCEEDED => 'Вы превысили лимит запросов на получение ответа по одной задаче. Попробуйте запрашивать результат задачи не чаще 1 раза в 2 секунды.',
            ErrorType::THE_DOMAIN_IS_NOT_ALLOWED => 'Капчу с некоторых доменов нельзя разгадывать в CapMonster Cloud. При попытке создать задание для такого домена вернётся эта ошибка.',
            ErrorType::THE_TOKEN_IS_EXPIRED => 'При попытке распознать капчу её провайдер сообщил, что истёк срок действия дополнительного токена. Попробуйте отправить капчу с новым токеном.',
            ErrorType::NO_FREE_SERVERS => 'На данный момент нет свободных серверов для распознавания этого задания. Повторите попытку через некоторое время. ',
            ErrorType::INVALID_RECAPTCHA_SITEKEY => 'Неверный sitekey.',
            ErrorType::INVALID_RECAPTCHA_DOMAIN => 'Домен не соответствует sitekey.',
            ErrorType::RECAPTCHA_TIMEOUT => 'Превышен таймаут решения рекапчи, скорее всего из-за медленного прокси-сервера или сервера Google.',
            ErrorType::YOUR_IP_IS_BLOCKED => 'Доступ к API с этого IP запрещен из-за большого количества ошибок.',
            ErrorType::FAILED_TO_CONNECT_PROXY => 'Не удалось подключиться к прокси-серверу, таймаут соединения. ',
            ErrorType::THE_PROXY_IP_IS_BANNED => 'IP прокси забанен на целевом сервисе капчи. ',
            ErrorType::INCORRECT_TASK_TYPE => 'Тип задачи не поддерживается или указан неверно. Проверьте свойство «type» в объекте задачи. ',
            ErrorType::INVALID_ARGUMENT_EXCEPTION => 'Получен неверный аргумент.',
            ErrorType::SEND_MESSAGE_ERROR => 'Ошибка при отправке запроса к сервису.',
            ErrorType::TYPE_TASK_RESOLVE_EXCEPTION => 'Неподдерживаемый тип задачи.',
            ErrorType::TIMEOUT_EXPIRED => 'Превышено время выполнения.',
            ErrorType::RESPONSE_ERROR => 'Ошибка при получении ответа от сервера.',
            ErrorType::RESPONSE_CODE_ERROR => 'Неверный код ответа сервера.',
            ErrorType::UNKNOWN_ERROR => 'Неизвестная ошибка сервера.',
        };
    }
}