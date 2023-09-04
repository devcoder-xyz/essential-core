<?php

namespace Essential\Core;

use Essential\Core\Manager\CacheManager;
use Essential\Core\Package\PackageInterface;

final class Dependency
{
    const CACHE_KEY = '__essential_app_dependency';

    private BaseKernel $baseKernel;

    public function __construct(BaseKernel $baseKernel)
    {
        $this->baseKernel = $baseKernel;
    }
    public function load(): array
    {
        if ($this->baseKernel->getEnv() == 'prod' && !empty($items)) {
            return $items;
        }

        $services = $this->loadConfigurationIfExists('services.php');
        $parameters = $this->loadParameters('parameters.php');
        $listeners = $this->loadConfigurationIfExists('listeners.php');
        $routes = $this->loadConfigurationIfExists('routes.php');
        $commands = $this->loadConfigurationIfExists('commands.php');
        $packages = $this->getPackages();
        foreach ($packages as $package) {
            $services = array_merge($package->getDefinitions(), $services);
            $parameters = array_merge($package->getParameters(), $parameters);
            $listeners = array_merge_recursive($package->getListeners(), $listeners);
            $routes = array_merge($package->getRoutes(), $routes);
            $commands = array_merge($package->getCommands(), $commands);
        }

        return [$services, $parameters, $listeners, $routes, $commands, $packages];
    }

    /**
     * @return array<PackageInterface>
     */
    private function getPackages(): array
    {
        $packagesName = $this->loadConfigurationIfExists('packages.php');
        $packages = [];
        foreach ($packagesName as $packageName => $envs) {
            if (!in_array($this->baseKernel->getEnv(), $envs)) {
                continue;
            }
            $packages[] = new $packageName();
        }
        return $packages;
    }

    private function loadConfigurationIfExists(string $fileName): array
    {
        $filePath = $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists($filePath)) {
            return require $filePath;
        }

        return [];
    }

    private function loadParameters(string $fileName): array
    {
        $parameters = $this->loadConfigurationIfExists($fileName);

        $parameters['essential.environment'] = $this->baseKernel->getEnv();
        $parameters['essential.debug'] = $this->baseKernel->getEnv() === 'dev';
        $parameters['essential.project_dir'] = $this->baseKernel->getProjectDir();
        $parameters['essential.cache_dir'] = $this->baseKernel->getCacheDir();
        $parameters['essential.logs_dir'] = $this->baseKernel->getLogDir();
        $parameters['essential.config_dir'] = $this->baseKernel->getConfigDir();
        $parameters['essential.public_dir'] = $this->baseKernel->getPublicDir();

        return $parameters;
    }
}
