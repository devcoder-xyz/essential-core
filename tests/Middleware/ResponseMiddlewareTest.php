<?php

namespace Test\Essential\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponseMiddlewareTest implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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

            public function getStatusCode(): int
            {
                return 200;
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
}