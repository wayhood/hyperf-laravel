<?php

declare(strict_types=1);

namespace Wayhood\HyperfLaravel;

use Wayhood\HyperfLaravel\Aspect\RouteCollectorAspect;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'aspects' => [
                RouteCollectorAspect::class,
            ],
        ];
    }
}
