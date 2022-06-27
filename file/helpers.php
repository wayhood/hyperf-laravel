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

        /** @var ResponseInterface $response */
        $response = Context::get(ResponseInterface::class);
        $response = $response->withStatus($status);
        $format = 'plain';

        foreach ($headers as $key => $value) {
            $response = $response->withAddedHeader(strtolower($key), $value);
            if (strtolower($key) === 'content-type' && str_contains("application/xml")) {
                $format = 'xml';
            }
        }

        if (is_string($content)) {
            $format = 'plain';
            $data = $content;
        }

        if ($format != 'xml' && is_array($content) && is_object($content)) {
            $format = 'json';
            $response = $response->withAddedHeader('content-type', 'application/json; charset=utf-8');
        }

        if ($format != 'plain') {
            $data = Json::encode($content);
        }

        return $response->withBody(new SwooleStream($data));
    }
}