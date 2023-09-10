<?php

namespace Test\Essential\Core;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Test\Essential\Core\Controller\SampleController;

class ControllerTest extends TestCase
{
    public function testMiddleware()
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $controller = new SampleController([$middleware]);

        $middlewares = $controller->getMiddlewares();

        $this->assertInstanceOf(MiddlewareInterface::class, $middlewares[0]);
    }

    public function testInvalidMiddleware()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invalidMiddleware = 'InvalidMiddlewareClass';
        $controller = new SampleController([$invalidMiddleware]);
    }

    public function testGet()
    {
        $controller = new SampleController([]);
        $container = $this->createMock(ContainerInterface::class);

        $controller->setContainer($container);

        $container->expects($this->once())
            ->method('get')
            ->with('service_id')
            ->willReturn('service_instance');

        $this->assertEquals('service_instance', $controller->testGet('service_id'));
    }
}