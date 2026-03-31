<?php

declare(strict_types=1);

namespace Tests\FakeHttpClient;

use CapMonsterClient\Enum\ApiMethod;
use Psr\Http\Message\ResponseInterface;
use Tests\Unit\AbstractTestCase;
use Webclient\Fake\Handler\SpecHandler\Rule;
use Webclient\Fake\Message\Stream;

final class GetBalanceHandlerFactory implements HandlerFactoryInterface
{
    public function __construct(
        private readonly string $balance
    ) {
    }

    public function setRouteRule(Rule $rule): void
    {
        $rule->allOf(function (Rule $rule) {
            $rule->match('body', sprintf('"clientKey".+"%s"', AbstractTestCase::SECRET_KEY));
            $rule->equal('uri.path', (ApiMethod::GET_BALANCE)->value);
            $rule->equal('method', 'POST');
        });
    }

    public function getResponse(ResponseInterface $response): ResponseInterface
    {
        $response->withStatus(200);
        if ($this->balance) {
            var_dump(preg_match('~^99.*?~', $this->balance));
            if (preg_match('~^99.*?~', $this->balance)) {

                return
                    $response->withBody(
                        new Stream(
                            json_encode(
                                [
                                    'errorId' => 1,
                                    'errorCode' => 'ERROR_ZERO_BALANCE',
                                    'errorDescription' => 'Account has zero balance. Add funds to continue recognition.',
                                ]
                            )
                        )
                    );
            }
            if (preg_match('~^98.*?~', $this->balance)) {

                return $response->withStatus(400);
            }
        }

        return
            $response->withBody(
                new Stream(
                    json_encode(
                        [
                            'errorId' => 0,
                            'balance' => $this->balance ?: rand(100000, 999999) / 100
                        ]
                    )
                )
            );
    }
}