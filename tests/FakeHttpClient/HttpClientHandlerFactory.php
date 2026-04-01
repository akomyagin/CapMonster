<?php

declare(strict_types=1);

namespace Tests\FakeHttpClient;

use CapMonsterClient\Enum\ApiMethod;
use Psr\Http\Message\ResponseInterface;
use Tests\Unit\AbstractTestCase;
use Webclient\Fake\Handler\SimpleRoutingHandler\SimpleRoutingHandler;
use Psr\Http\Server\RequestHandlerInterface;
use Webclient\Fake\Handler\SpecHandler\Rule;
use Webclient\Fake\Message\Stream;

final class HttpClientHandlerFactory
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly array $fieldValue = []
    ) {
    }

    public function create(): RequestHandlerInterface
    {
        $handler = new SimpleRoutingHandler(
            HandlerBuilder::build(
                [$this, 'createNotFoundHandler'],
                [$this, 'createResponseNotFound']
            )
        );
        $handler
            ->route(
                ['POST'],
                implode('/', [$this->baseUrl, (ApiMethod::GET_BALANCE)->value]),
                $this->createHandler(
                    new GetBalanceHandlerFactory(
                        (string) ($this->fieldValue['balance'] ?? '')
                    )
                )
            )
            ->route(
                ['POST'],
                implode('/', [$this->baseUrl, (ApiMethod::CREATE_TASK)->value]),
                $this->createTaskHandler()
            )
            ->route(
                ['POST'],
                implode('/', [$this->baseUrl, (ApiMethod::GET_TASK_RESULT)->value]),
                $this->getTaskResultHandler()
            )
        ;

        return $handler;
    }

    private function createHandler(HandlerFactoryInterface $handlerFactory): RequestHandlerInterface
    {
        return
            HandlerBuilder::build(
                [$handlerFactory, 'setRouteRule'],
                [$handlerFactory, 'getResponse']
            );
    }

    private function getUrl(ApiMethod $method): string
    {
        return '/' . $method->value;
    }

    public function createNotFoundHandler(Rule $rule): void
    {
    }

    public function createResponseNotFound(ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(404);
    }

    private function createTaskHandler(): RequestHandlerInterface
    {
        return
            HandlerBuilder::build(
                [$this, 'createNewTaskHandler'],
                [$this, 'createNewTaskResponse']
            );
    }

    public function createNewTaskHandler(Rule $rule): void
    {
        $this->setRule($rule, ApiMethod::CREATE_TASK);
    }

    public function createNewTaskResponse(ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(200)->withBody(new Stream(json_encode([
            'errorId' => 0,
            'taskId' => 7654321,
        ])));
    }

    private function getTaskResultHandler(): RequestHandlerInterface
    {
        $sequence = $this->fieldValue['getTaskResultSequence'] ?? null;
        if (is_array($sequence) && $sequence !== []) {
            $counter = 0;

            return HandlerBuilder::build(
                [$this, 'createGetTaskResultHandler'],
                function (ResponseInterface $response) use (&$counter, $sequence): ResponseInterface {
                    $idx = min($counter, count($sequence) - 1);
                    $payload = $sequence[$idx];
                    if ($counter < count($sequence) - 1) {
                        ++$counter;
                    }
                    $body = is_string($payload) ? $payload : json_encode($payload, JSON_THROW_ON_ERROR);

                    return $response->withStatus(200)->withBody(new Stream($body));
                }
            );
        }

        return
            HandlerBuilder::build(
                [$this, 'createGetTaskResultHandler'],
                [$this, 'createGetTaskResultResponse']
            );
    }

    public function createGetTaskResultHandler(Rule $rule): void
    {
        $this->setRule($rule, ApiMethod::GET_TASK_RESULT);
    }

    public function createGetTaskResultResponse(ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(200)->withBody(new Stream(json_encode([
            'errorId' => 0,
            'status' => 'ready',
            'solution' => [
                'gRecaptchaResponse' => 'fake-response-token',
            ],
        ])));
    }

    public function setRule(Rule $rule, ApiMethod $method): void
    {
        $rule->allOf(function (Rule $rule) use ($method) {
            $rule->match('body', sprintf('"clientKey".+"%s"', AbstractTestCase::SECRET_KEY));
            $rule->equal('uri.path', $this->getUrl($method));
            $rule->equal('method', 'POST');
        });
    }
}