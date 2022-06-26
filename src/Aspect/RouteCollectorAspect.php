<?php
namespace Wayhood\HyperfLaravel\Aspect;

use FastRoute\DataGenerator;
use FastRoute\RouteParser;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\Handler;

class RouteCollectorAspect extends AbstractAspect
{
    public $classes = [
        'Hyperf\HttpServer\Router\RouteCollector::addRoute',
    ];

    protected string $currentGroupPrefix = '';

    protected array $currentGroupOptions = [];
    
    protected RouteParser $routeParser;
    
    protected DataGenerator $dataGenerator;
    
    protected string $server;
    
    private function getProperty(\ReflectionClass $reflectionClass, string $propertyName)
    {
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($proceedingJoinPoint->getInstance());
    }

    protected function mergeOptions(array $origin, array $options): array
    {
        return array_merge_recursive($origin, $options);
    }
    
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $httpMethod = $proceedingJoinPoint->getArguments()[0];
        $route = $proceedingJoinPoint->getArguments()[1];
        $handler = $proceedingJoinPoint->getArguments()[2];
        $options = $proceedingJoinPoint->getArguments()[3];

        $reflectionClass = new \ReflectionClass($proceedingJoinPoint->getInstance());
        
        $this->currentGroupPrefix = $this->getProperty($reflectionClass, 'currentGroupPrefix');
        $this->routeParser = $this->getProperty($reflectionClass, 'routeParser');
        $this->currentGroupOptions = $this->getProperty($reflectionClass, 'currentGroupOptions');
        $this->dataGenerator = $this->getProperty($reflectionClass, 'dataGenerator');

        $route = $this->currentGroupPrefix . $route;
        $routeDataList = $this->routeParser->parse($route);
        $newRouteDataList = [];

        foreach($routeDataList as $routeData) {
            if (count($routeData) == 1) {
                $newRouteDataList[] = $routeData;
                if (str_ends_with($routeData[0], '/')) {
                    $newRouteDataList[] = [rtrim($routeData[0], '/')];
                }
            } else {
                $newRouteDataList[] = $routeData;
            }
        }
        $routeDataList = $newRouteDataList;

        $options = $this->mergeOptions($this->currentGroupOptions, $options);
        foreach ((array) $httpMethod as $method) {
            $method = strtoupper($method);
            foreach ($routeDataList as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, new Handler($handler, $route, $options));
            }

            MiddlewareManager::addMiddlewares($this->server, $route, $method, $options['middleware'] ?? []);
        }
        return;
    }
}