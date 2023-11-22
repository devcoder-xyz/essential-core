<?php

namespace Test\Essential\Core\Kernel;

use Essential\Core\BaseKernel;

class  SampleKernelTest extends BaseKernel
{
    private string $envfile;

    public function __construct(string $envfile)
    {
        $this->envfile = $envfile;
        parent::__construct();
    }

    public function getProjectDir(): string
    {
       return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return '';
    }

    public function getLogDir(): string
    {
        return '';
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    public function getPublicDir(): string
    {
        return '';
    }

    public function getEnvFile(): string
    {
        return $this->getProjectDir() . DIRECTORY_SEPARATOR . $this->envfile;
    }

    protected function afterBoot(): void
    {
        // TODO: Implement afterBoot() method.
    }
}
