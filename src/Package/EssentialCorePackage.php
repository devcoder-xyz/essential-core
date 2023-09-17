<?php

namespace Essential\Core\Package;

use DevCoder\Listener\EventDispatcher;
use DevCoder\Listener\ListenerProvider;
use DevCoder\Renderer\PhpRenderer;
use DevCoder\Route;
use DevCoder\Router as DevCoderRouter;
use Essential\Core\Command\CacheClearCommand;
use Essential\Core\Command\DebugContainerCommand;
use Essential\Core\Command\DebugEnvCommand;
use Essential\Core\Command\DebugRouteCommand;
use Essential\Core\Command\MakeCommandCommand;
use Essential\Core\Command\MakeControllerCommand;
use Essential\Core\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Essential\Core\ErrorHandler\ExceptionHandler;
use Essential\Core\Middlewares\RouterMiddleware;
use Essential\Core\Router\Bridge\RouteFactory;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Application;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function getenv;

final class EssentialCorePackage implements PackageInterface
{
    public function getDefinitions(): array
    {
        return [
                EventDispatcherInterface::class => static function (ContainerInterface $container): ?EventDispatcherInterface {

                    if (!class_exists(EventDispatcher::class)) {
                        throw new LogicException('The "EventDispatcherInterface" requires the presence of an event dispatcher. You can install it by running "composer require devcoder-xyz/php-event-dispatcher".');
                    }

                    $events = $container->get('essential.listeners');
                    $provider = new ListenerProvider();
                    foreach ($events as $event => $listeners) {
                        if (is_array($listeners)) {
                            foreach ($listeners as $listener) {
                                $provider->addListener($event, $container->get($listener));
                            }
                        } elseif (is_object($listeners)) {
                            $provider->addListener($event, $listeners);
                        } else {
                            $provider->addListener($event, $container->get($listeners));
                        }
                    }
                    return new EventDispatcher($provider);
                },
                Application::class => static function (ContainerInterface $container): Application {
                    $commandList = $container->get('essential.commands');
                    $commands = [];
                    foreach ($commandList as $commandName) {
                        $commands[] = $container->get($commandName);
                    }
                    $application = new Application();
                    $application->addCommands($commands);
                    return $application;
                },
                'render' => static function (ContainerInterface $container) {
                    if (class_exists(Environment::class)) {
                        $loader = new FilesystemLoader($container->get('app.template_dir'));
                        return new Environment($loader, [
                            'debug' => $container->get('essential.debug'),
                            'cache' => $container->get('essential.environment') == 'dev' ? false : $container->get('essential.cache_dir'),
                        ]);
                    } elseif (class_exists(PhpRenderer::class)) {
                        return new PhpRenderer($container->get('app.template_dir'));
                    }

                    throw new LogicException('The "render" requires a Renderer to be available. You can choose between installing "devcoder-xyz/php-renderer" or "twig/twig" depending on your preference.');
                },

                'router' => static function (ContainerInterface $container): object {

                    if (!class_exists(DevCoderRouter::class)) {
                        throw new LogicException('The Router component requires the presence of a router library. You can install it by running "composer require devcoder-xyz/php-router".');
                    }

                    /**
                     * @var array<\Essential\Core\Router\Route> $routes
                     */
                    $routes = $container->get('essential.routes');
                    $factory = new RouteFactory();

                    $router = new Router([], $container->get('app.url'));
                    foreach ($routes as $route) {
                        $router->add($factory->createDevCoderRoute($route));
                    }
                    return $router;
                },
                RouterMiddleware::class => static function (ContainerInterface $container) {
                    return new RouterMiddleware($container->get('router'), response_factory());
                },
                ExceptionHandler::class => static function (ContainerInterface $container) {
                    return new ExceptionHandler(response_factory(), [
                            'debug' => $container->get('essential.debug'),
                            'html_response' => new HtmlErrorRenderer(
                                response_factory(),
                                $container->get('essential.debug'),
                                $container->get('app.template_dir') . DIRECTORY_SEPARATOR . '_exception'
                            )
                        ]
                    );
                }
            ];
    }

    public function getParameters(): array
    {
        return [
            'app.url' => getenv('APP_URL') ?: '',
            'app.locale' => getenv('APP_LOCALE') ?: 'en',
            'app.template_dir' => getenv('APP_TEMPLATE_DIR') ?: '',
        ];
    }

    public function getRoutes(): array
    {
        return [];
    }

    public function getListeners(): array
    {
        return [];
    }

    public function getCommands(): array
    {
        return [
            CacheClearCommand::class,
            MakeControllerCommand::class,
            MakeCommandCommand::class,
            DebugEnvCommand::class,
            DebugContainerCommand::class,
            DebugRouteCommand::class,
        ];
    }
}
