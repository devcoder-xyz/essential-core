<?php

namespace Essential\Core;

use Essential\Core\Package\PackageInterface;

final class Dependency
{
    private BaseKernel $baseKernel;

    public function __construct(BaseKernel $baseKernel)
    {
        $this->baseKernel = $baseKernel;
    }

    public function load(): array
    {
        $services = (require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'services.php');
        $parameters = $this->loadParameters(require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'parameters.php');
        $listeners = (require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'listeners.php');
        $routes = (require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'routes.php');
        $commands = (require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'commands.php');
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
        $packagesName = (require $this->baseKernel->getConfigDir() . DIRECTORY_SEPARATOR . 'packages.php');
        $packages = [];
        foreach ($packagesName as $packageName => $envs) {
            if (!in_array($this->baseKernel->getEnv(), $envs)) {
                continue;
            }
            $packages[] = new $packageName();
        }
        return $packages;
    }

    private function loadParameters(array $parameters): array
    {
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
