<?php


use DevCoder\DependencyInjection\Container;
use DevCoder\DependencyInjection\ReflectionResolver;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

return [
    'server_request' => static function (): ServerRequestInterface {
        return new class implements ServerRequestInterface {

            public function getProtocolVersion()
            {
                // TODO: Implement getProtocolVersion() method.
            }

            public function withProtocolVersion(string $version)
            {
                // TODO: Implement withProtocolVersion() method.
            }

            public function getHeaders()
            {
                // TODO: Implement getHeaders() method.
            }

            public function hasHeader(string $name)
            {
                // TODO: Implement hasHeader() method.
            }

            public function getHeader(string $name)
            {
                // TODO: Implement getHeader() method.
            }

            public function getHeaderLine(string $name)
            {
                // TODO: Implement getHeaderLine() method.
            }

            public function withHeader(string $name, $value)
            {
                // TODO: Implement withHeader() method.
            }

            public function withAddedHeader(string $name, $value)
            {
                // TODO: Implement withAddedHeader() method.
            }

            public function withoutHeader(string $name)
            {
                // TODO: Implement withoutHeader() method.
            }

            public function getBody()
            {
                // TODO: Implement getBody() method.
            }

            public function withBody(StreamInterface $body)
            {
                // TODO: Implement withBody() method.
            }

            public function getRequestTarget()
            {
                // TODO: Implement getRequestTarget() method.
            }

            public function withRequestTarget(string $requestTarget)
            {
                // TODO: Implement withRequestTarget() method.
            }

            public function getMethod()
            {
                // TODO: Implement getMethod() method.
            }

            public function withMethod(string $method)
            {
                // TODO: Implement withMethod() method.
            }

            public function getUri(): UriInterface
            {
                // TODO: Implement getUri() method.
            }

            public function withUri(UriInterface $uri, bool $preserveHost = false)
            {
                // TODO: Implement withUri() method.
            }

            public function getServerParams()
            {
                // TODO: Implement getServerParams() method.
            }

            public function getCookieParams()
            {
                // TODO: Implement getCookieParams() method.
            }

            public function withCookieParams(array $cookies)
            {
                // TODO: Implement withCookieParams() method.
            }

            public function getQueryParams()
            {
                // TODO: Implement getQueryParams() method.
            }

            public function withQueryParams(array $query)
            {
                // TODO: Implement withQueryParams() method.
            }

            public function getUploadedFiles()
            {
                // TODO: Implement getUploadedFiles() method.
            }

            public function withUploadedFiles(array $uploadedFiles)
            {
                // TODO: Implement withUploadedFiles() method.
            }

            public function getParsedBody()
            {
                // TODO: Implement getParsedBody() method.
            }

            public function withParsedBody($data)
            {
                // TODO: Implement withParsedBody() method.
            }

            public function getAttributes()
            {
                // TODO: Implement getAttributes() method.
            }

            public function getAttribute(string $name, $default = null)
            {
                // TODO: Implement getAttribute() method.
            }

            public function withAttribute(string $name, $value)
            {
                // TODO: Implement withAttribute() method.
            }

            public function withoutAttribute(string $name)
            {
                // TODO: Implement withoutAttribute() method.
            }
        };
    },
    'response_factory' => static function (): ResponseFactoryInterface {
        return new class implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new class implements ResponseInterface {

                    public function getProtocolVersion()
                    {
                        // TODO: Implement getProtocolVersion() method.
                    }

                    public function withProtocolVersion(string $version)
                    {
                        // TODO: Implement withProtocolVersion() method.
                    }

                    public function getHeaders()
                    {
                        // TODO: Implement getHeaders() method.
                    }

                    public function hasHeader(string $name)
                    {
                        // TODO: Implement hasHeader() method.
                    }

                    public function getHeader(string $name)
                    {
                        // TODO: Implement getHeader() method.
                    }

                    public function getHeaderLine(string $name)
                    {
                        // TODO: Implement getHeaderLine() method.
                    }

                    public function withHeader(string $name, $value)
                    {
                        // TODO: Implement withHeader() method.
                    }

                    public function withAddedHeader(string $name, $value)
                    {
                        // TODO: Implement withAddedHeader() method.
                    }

                    public function withoutHeader(string $name)
                    {
                        // TODO: Implement withoutHeader() method.
                    }

                    public function getBody()
                    {
                        // TODO: Implement getBody() method.
                    }

                    public function withBody(StreamInterface $body)
                    {
                        // TODO: Implement withBody() method.
                    }

                    public function getStatusCode()
                    {
                        // TODO: Implement getStatusCode() method.
                    }

                    public function withStatus(int $code, string $reasonPhrase = '')
                    {
                        // TODO: Implement withStatus() method.
                    }

                    public function getReasonPhrase()
                    {
                        // TODO: Implement getReasonPhrase() method.
                    }
                };
            }
        };
    },
    'container' => static function (array $definitions, array $options): ContainerInterface {

        return new class($definitions) implements ContainerInterface  {

            private array $definitions;

            public function __construct(array $definitions)
            {
                $this->definitions = $definitions;
            }

            /**
             * @param string $id
             * @return mixed
             */
            public function get(string $id)
            {
                return $this->definitions[$id] ?? null;
            }

            public function has(string $id)
            {
                return array_key_exists($id, $this->definitions);
            }
        };
    },
    'custom_environments' => ['test'],
];