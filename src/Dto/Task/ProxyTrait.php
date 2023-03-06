<?php

namespace CapMonsterClient\Dto\Task;

trait ProxyTrait
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

    private readonly string $proxyType;

    private readonly string $proxyAddress;

    private readonly int $proxyPort;

    private readonly ?string $proxyLogin;

    private readonly ?string $proxyPassword;

    private function proxyTraitInit(
        string $proxyType,
        string $proxyAddress,
        int $proxyPort,
        ?string $proxyLogin = null,
        ?string $proxyPassword = null
    ): void {
        $this->proxyType = $proxyType;
        $this->proxyAddress = $proxyAddress;
        $this->proxyPort = $proxyPort;
        $this->proxyLogin = $proxyLogin;
        $this->proxyPassword = $proxyPassword;
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