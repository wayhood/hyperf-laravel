<?php

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Context\Context;

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     * @param mixed $content
     * @param $status
     * @param array $headers
     * @return mixed
     */
    function response(mixed $content = '', $status = 200, array $headers = []): ResponseInterface
    {
        $data = $result = Json::encode($content);
        /** @var ResponseInterface $response */
        $response = Context::get(ResponseInterface::class);

        $response = $response->withStatus($status);
        foreach ($headers as $key => $value) {
            $response = $response->withAddedHeader(strtolower($key), $value);
        }
        $response = $response->withAddedHeader('content-type', 'application/json; charset=utf-8');
        return $response->withBody(new SwooleStream($data));
    }
}