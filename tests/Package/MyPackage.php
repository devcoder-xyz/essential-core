<?php

namespace Test\Essential\Core\Package;

use Essential\Core\Command\CacheClearCommand;
use Essential\Core\Command\MakeCommandCommand;
use Essential\Core\Package\PackageInterface;
use Essential\Core\Router\Route;
use Psr\Container\ContainerInterface;

class MyPackage implements PackageInterface
{
    public function getDefinitions(): array
    {
        return [
            'router' => static function (ContainerInterface $container) {
                return new \stdClass();
            },
            'render' => static function (ContainerInterface $container) {
                return new \stdClass();
            },
        ];
    }

    public function getParameters(): array
    {
        return [
            'app.url' => 'https://example.com',
            'app.locale' => 'en',
            'app.template_dir' => '/path/to/templates',
        ];
    }

    public function getRoutes(): array
    {
        return [
            new Route('example', '/example', function () {}),
            new Route('another', '/another', function () {}),
        ];
    }

    public function getListeners(): array
    {
        return [
            'App\\Event\\ExampleEvent' => \stdClass::class,
            'App\\Event\\AnotherEvent' => \stdClass::class,
        ];
    }

    public function getCommands(): array
    {
        return [
            CacheClearCommand::class,
            MakeCommandCommand::class,
        ];
    }
}