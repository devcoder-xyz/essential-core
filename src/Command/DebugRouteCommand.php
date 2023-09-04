<?php
declare(strict_types=1);

namespace Essential\Core\Command;

use Essential\Core\Router\Route;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugRouteCommand extends Command
{
    protected static $defaultName = 'debug:routes';
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('List all registered routes in the application');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Registered Routes');

        /**
         * @var array<Route> $routes
         */
        $routes = $this->container->get('essential.routes');
        $formattedRoutes = array_map(function (Route $route) {
            return [
                'Name' => $route->getName(),
                'Method' => implode('|', $route->getMethods()),
                'Path' => $route->getPath(),
            ];
        }, $routes);

        uasort($formattedRoutes, function ($a, $b) {
            return strcasecmp($a['Name'], $b['Name']);
        });

        $io->table(
            ['Name', 'Method', 'Path'],
            $formattedRoutes
        );

        return Command::SUCCESS;
    }
}
