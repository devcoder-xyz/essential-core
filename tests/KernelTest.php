<?php

namespace Test\Essential\Core;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Test\Essential\Core\Kernel\SampleKernel;

class KernelTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['APP_ENV']);
        unset($_SERVER['APP_ENV']);
        unset($_ENV['APP_TIMEZONE']);
        unset($_SERVER['APP_TIMEZONE']);
        unset($_ENV['APP_LOCALE']);
        unset($_SERVER['APP_LOCALE']);
        unset($_ENV['APP_URL']);
        unset($_SERVER['APP_URL']);

        putenv('APP_ENV');
        putenv('APP_TIMEZONE');
        putenv('APP_LOCALE');
        putenv('APP_URL');


        date_default_timezone_set('UTC');
    }
    public function testLoadKernel()
    {
        $baseKernel = new SampleKernel('.env');
        $this->assertEquals('dev', $baseKernel->getEnv());
        $this->assertEquals('dev', getenv('APP_ENV'));
        $this->assertEquals('Europe/Paris', getenv('APP_TIMEZONE'));
        $this->assertEquals('fr', getenv('APP_LOCALE'));
        $this->assertEquals('http://localhost', getenv('APP_URL'));
        $this->assertEquals('Europe/Paris', date_default_timezone_get());
    }

    public function testLoadConfigurationIfExists()
    {
        $baseKernel = new SampleKernel('.env');
        $this->assertEquals([], $baseKernel->loadConfigurationIfExists('test.php'));
    }

    public function testDefaultValue()
    {
        $baseKernel = new SampleKernel('.env.test');
        $this->assertEquals('prod', $baseKernel->getEnv());
        $this->assertEquals('prod', getenv('APP_ENV'));
        $this->assertEquals('UTC', getenv('APP_TIMEZONE'));
        $this->assertEquals('en', getenv('APP_LOCALE'));
        $this->assertFalse(getenv('APP_URL'));
        $this->assertEquals('UTC', date_default_timezone_get());
    }

    public function testKernelContainer()
    {
        $baseKernel = new SampleKernel('.env');
        $this->assertInstanceOf(ContainerInterface::class, $baseKernel->getContainer());
    }
}
