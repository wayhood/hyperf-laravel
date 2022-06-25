<?php
namespace Hyperf\HttpServer {

    class Request
    {
        public function only($keys): array
        {
        }

        public function get(): mixed
        {
        }
    }
}

namespace Hyperf\HttpServer\Contract {
    interface RequestInterface
    {
        public function only($keys): array;

        public function get(): mixed;
    }
}