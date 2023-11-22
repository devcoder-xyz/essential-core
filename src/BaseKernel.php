<?php
declare(strict_types=1);

namespace Essential\Core;

use DateTimeImmutable;
use DevCoder\DotEnv;
use Essential\Core\ErrorHandler\ErrorHandler;
use Essential\Core\ErrorHandler\ExceptionHandler;
use Essential\Core\Handler\RequestHandler;
use Essential\Core\Http\Exception\HttpExceptionInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Throwable;
use function array_filter;
use function array_keys;
use function array_merge;
use function date_default_timezone_set;
use function error_reporting;
use function getenv;
use function implode;
use function in_array;
use function json_encode;
use function sprintf;

/**
 * @package    Essential
 * @author    Devcoder.xyz <dev@devcoder.xyz>
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link    https://www.devcoder.xyz
 */
abstract class BaseKernel
{
    private const DEFAULT_ENV = 'prod';
    public const VERSION = '0.1.0-alpha';
    public const NAME = 'Essential';
    private const DEFAULT_ENVIRONMENTS = [
        'dev',
        'prod'
    ];
    private string $env = self::DEFAULT_ENV;
    protected ContainerInterface $container;
    /**
     * @var array<MiddlewareInterface, string>
     */
    private array $middlewareCollection = [];
    protected ?float $startTime = null;

    /**
     * BaseKernel constructor.
     */
    public function __construct()
    {
        App::init($this->loadConfigurationIfExists('framework.php'));
        $this->boot();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Throwable
     */
    final public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $requestHandler = new RequestHandler($this->container, $this->middlewareCollection);
            $response = $requestHandler->handle($request);
            if ($this->startTime !== null) {
                $diff = (microtime(true) - $this->startTime);
                $this->log([
                    'request' => $request->getUri()->getPath(),
                    'load_time_ms' => $diff * 1000 . ' ms',
                    'load_time_second' => number_format($diff, 3) . ' s',
                    'environment' => $this->getEnv(),
                ]);
            }
            return $response;
        } catch (Throwable $exception) {
            if (!$exception instanceof HttpExceptionInterface) {
                $this->logException($exception);
            }

            $exceptionHandler = $this->container->get(ExceptionHandler::class);
            return $exceptionHandler->render($request, $exception);
        }
    }

    final public function getEnv(): string
    {
        return $this->env;
    }

    final public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    abstract public function getProjectDir(): string;

    abstract public function getCacheDir(): string;

    abstract public function getLogDir(): string;

    abstract public function getConfigDir(): string;

    abstract public function getPublicDir(): string;

    abstract public function getEnvFile(): string;

    abstract protected function afterBoot(): void;

    protected function loadContainer(array $definitions): ContainerInterface
    {
        return App::createContainer($definitions, ['cache_dir' => $this->getCacheDir()]);
    }

    final protected function logException(Throwable $exception): void
    {
        $this->log([
            'date' => (new DateTimeImmutable())->format('c'),
            'id' => uniqid('ERR'),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ]);
    }

    final protected function log(array $data): void
    {
        error_log(
            json_encode($data) . PHP_EOL,
            3,
            $this->getLogDir() . DIRECTORY_SEPARATOR . $this->getEnv() . '.log'
        );
    }

    final private function boot(): void
    {
        $this->initEnv();

        date_default_timezone_set(getenv('APP_TIMEZONE'));

        error_reporting(0);
        if ($this->getEnv() === 'dev') {
            $this->startTime = microtime(true);
            ErrorHandler::register();
        }

        $middleware = $this->loadConfigurationIfExists('middleware.php');
        $middleware = array_filter($middleware, function ($environments) {
            return in_array($this->getEnv(), $environments);
        });
        $this->middlewareCollection = array_keys($middleware);

        list($services, $parameters, $listeners, $routes, $commands, $packages) = (new Dependency($this))->load();
        $definitions = array_merge(
            $parameters,
            $services,
            [
                'essential.packages' => $packages,
                'essential.commands' => $commands,
                'essential.listeners' => $listeners,
                'essential.routes' => $routes,
                'essential.middleware' => $this->middlewareCollection,
                BaseKernel::class => $this
            ]
        );
        $definitions['essential.services_ids'] = array_keys($definitions);

        $this->container = $this->loadContainer($definitions);
        $this->afterBoot();
    }

    final private function initEnv(): void
    {
        (new DotEnv($this->getEnvFile()))->load();
        foreach (['APP_ENV' => self::DEFAULT_ENV, 'APP_TIMEZONE' => 'UTC', 'APP_LOCALE' => 'en'] as $k => $value) {
            if (getenv($k) === false) {
                self::putEnv($k, $value);
            }
        }

        $environments = self::getAvailableEnvironments();
        if (!in_array(getenv('APP_ENV'), $environments)) {
            throw new InvalidArgumentException(sprintf(
                    'The env "%s" do not exist. Defined environments are: "%s".',
                    getenv('APP_ENV'),
                    implode('", "', $environments))
            );
        }
        $this->env = getenv('APP_ENV');
    }

    final public function loadConfigurationIfExists(string $fileName): array
    {
        $filePath = $this->getConfigDir() . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($filePath)) {
            return require $filePath;
        }

        return [];
    }

    final private static function getAvailableEnvironments(): array
    {
        return array_unique(array_merge(self::DEFAULT_ENVIRONMENTS, App::getCustomEnvironments()));
    }

    final private static function putEnv(string $name, $value): void
    {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}
