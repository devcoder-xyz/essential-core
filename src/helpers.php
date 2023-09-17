<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Essential\Core\App;
use Psr\Http\Message\ServerRequestInterface;

if (!function_exists('essential_composer_loader')) {

    /**
     * Returns the instance of the Composer class loader.
     *
     * @return \Composer\Autoload\ClassLoader
     * @throws \LogicException If the ESSENTIAL_COMPOSER_AUTOLOAD_FILE constant is not defined.
     */
    function essential_composer_loader(): \Composer\Autoload\ClassLoader
    {
        if (!defined('ESSENTIAL_COMPOSER_AUTOLOAD_FILE')) {
            throw new LogicException('ESSENTIAL_COMPOSER_AUTOLOAD_FILE const must be defined!');
        }
        return require ESSENTIAL_COMPOSER_AUTOLOAD_FILE;
    }
}

if (!function_exists('send')) {

    /**
     * Sends the HTTP response to the client.
     *
     * @param ResponseInterface $response The HTTP response to send.
     */
    function send(ResponseInterface $response)
    {
        $httpLine = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        if (!headers_sent()) {
            header($httpLine, true, $response->getStatusCode());

            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header("$name: $value", false);
                }
            }
        }

        echo $response->getBody();
    }
}

if (!function_exists('container')) {

    /**
     * Retrieves the application's dependency injection container.
     *
     * @return ContainerInterface The dependency injection container.
     */
    function container(): ContainerInterface
    {
        return App::getContainer();
    }
}

if (!function_exists('create_request')) {

    /**
     * Creates a new HTTP request.
     *
     * @return ServerRequestInterface The HTTP response.
     */
    function create_request(): ServerRequestInterface
    {
        return App::createServerRequest();
    }
}

if (!function_exists('response_factory')) {

    /**
     * Retrieves the response factory.
     *
     * @return ResponseFactoryInterface The response factory.
     */
    function response_factory(): ResponseFactoryInterface
    {
        return App::getResponseFactory();
    }
}

if (!function_exists('response')) {

    /**
     * Creates a new HTTP response.
     *
     * @param string $content The response content.
     * @param int $status The HTTP status code.
     * @return ResponseInterface The HTTP response.
     */
    function response(string $content = '', int $status = 200): ResponseInterface
    {
        $response = response_factory()->createResponse($status);
        $response->getBody()->write($content);
        return $response;
    }
}

if (!function_exists('json_response')) {

    /**
     * Creates a new JSON response.
     *
     * @param array $data The data to encode to JSON.
     * @param int $status The HTTP status code.
     * @param int $flags JSON encoding flags.
     * @return ResponseInterface The JSON response.
     * @throws InvalidArgumentException If JSON encoding fails.
     */
    function json_response(array $data, int $status = 200, int $flags = JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
    | JSON_UNESCAPED_SLASHES): ResponseInterface
    {
        $response = response_factory()->createResponse($status);
        $response->getBody()->write(json_encode($data, $flags));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                sprintf('Unable to encode data to JSON: %s', json_last_error_msg())
            );
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}

if (!function_exists('redirect')) {

    /**
     * Creates a redirect response.
     *
     * @param string $url The URL to redirect to.
     * @param int $status The HTTP status code.
     * @return ResponseInterface The redirect response.
     */
    function redirect(string $url, int $status = 302): ResponseInterface
    {
        $response = response_factory()->createResponse($status);
        return $response->withHeader('Location', $url);
    }
}

if (!function_exists('render_view')) {

    /**
     * Renders a view template with the provided context.
     *
     * @param string $view The name of the view template.
     * @param array $context The context data to pass to the view.
     * @return string The rendered view.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function render_view(string $view, array $context = []): string
    {
        if (!container()->has('render')) {
            throw new \LogicException('The "render_view" method requires a Renderer to be available. You can choose between installing "devcoder-xyz/php-renderer" or "twig/twig" depending on your preference.');
        }

        $renderer = container()->get('render');
        return $renderer->render($view, $context);
    }
}

if (!function_exists('render')) {

    /**
     * Renders a view template and creates an HTTP response.
     *
     * @param string $view The name of the view template.
     * @param array $context The context data to pass to the view.
     * @param int $status The HTTP status code.
     * @return ResponseInterface The HTTP response with the rendered view.
     */
    function render(string $view, array $context = [], int $status = 200): ResponseInterface
    {
        return response(render_view($view, $context), $status);
    }
}

if (!function_exists('__e')) {

    /**
     * Encodes a string for HTML entities.
     *
     * @param string $str The string to encode.
     * @param int $flags Flags for htmlentities.
     * @param string $encoding The character encoding.
     * @return string The encoded string.
     */
    function __e(string $str, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): string
    {
        return htmlentities($str, $flags, $encoding);
    }
}

if (!function_exists('dd')) {

    /**
     * Dump and die: Dumps data and exits the script.
     *
     * @param mixed ...$data The data to dump.
     */
    function dd(...$data)
    {
        dump(...$data);
        exit;
    }
}

if (!function_exists('dump')) {

    /**
     * Dump data to the output.
     *
     * @param mixed ...$data The data to dump.
     */
    function dump(...$data)
    {
        if(php_sapi_name() === 'cli') {
            var_dump($data);
            return;
        }
        echo '<pre style="font-size: 14px; font-family: Monaco, monospace; background-color: #f5f5f5; border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);">';

        foreach ($data as $item) {
            echo '<div style="background-color: #fff; border: 1px solid #ddd; padding: 5px; margin-bottom: 10px;">';
            var_dump($item);
            echo '</div>';
        }

        echo '</pre>';
    }
}
