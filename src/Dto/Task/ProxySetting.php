<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

final class ProxySetting
{
    /*
     * параметр proxyType тип String обязательно
     * http - обычный http/https прокси
     * https - попробуйте эту опцию только если "http" не работает (требуется для некоторых кастомных прокси)
     * socks4 - socks4 прокси
     * socks5 - socks5 прокси
     *
     * параметр proxyAddress тип String обязательно
     * IP адрес прокси IPv4/IPv6. Не допускается:
     *     использование имен хостов
     *     использование прозрачных прокси (там где можно видеть IP клиента)
     *     использование прокси на локальных машинах
     *
     * параметр proxyPort тип Integer обязательно
     * Порт прокси
     *
     * параметр proxyLogin тип String не обязательно
     * Логин прокси-сервера
     *
     * параметр proxyPassword тип String не обязательно
     * Пароль прокси-сервера
     */

    private function __construct(
        private readonly string $proxyType,
        private readonly string $proxyAddress,
        private readonly int $proxyPort,
        private readonly ?string $proxyLogin = null,
        private readonly ?string $proxyPassword = null
    ) {
    }

    public static function create(
        string $proxyType,
        string $proxyAddress,
        int $proxyPort,
        ?string $proxyLogin = null,
        ?string $proxyPassword = null
    ): self {
        return new self($proxyType, $proxyAddress, $proxyPort, $proxyLogin, $proxyPassword);
    }

    public function getProxyType(): string
    {
        return $this->proxyType;
    }

    public function getProxyAddress(): string
    {
        return $this->proxyAddress;
    }

    public function getProxyPort(): int
    {
        return $this->proxyPort;
    }

    public function getProxyLogin(): ?string
    {
        return $this->proxyLogin;
    }

    public function getProxyPassword(): ?string
    {
        return $this->proxyPassword;
    }
}