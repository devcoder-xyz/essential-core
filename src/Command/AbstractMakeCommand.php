<?php
declare(strict_types=1);

namespace Essential\Core\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;

abstract class AbstractMakeCommand extends Command
{
    abstract protected function template(string $classNamespace, string $curtClassName): string;

    protected function createClass(string $className): string
    {
        $namespaceArray = explode('\\', $className);
        $curtClassName = array_pop($namespaceArray);

        $classFilePath = self::getFilePathFromPsr4($className);
        $classNamespace = rtrim(substr($className, 0, -strlen($curtClassName)), '\\');

        if (!is_dir(dirname($classFilePath))) {
            mkdir(dirname($classFilePath), 0777, true);
        }

        file_put_contents($classFilePath, $this->template($classNamespace, $curtClassName));

        return $classFilePath;
    }

    private static function getFilePathFromPsr4(string $controllerName): string
    {
        $loader = essential_composer_loader();
        foreach ($loader->getPrefixesPsr4() as $namespace => $paths) {
            foreach ($paths as $path) {
                if (str_starts_with($controllerName, $namespace)) {
                    $path = realpath($path);
                    return $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, substr($controllerName, strlen($namespace))) . '.php';
                }
            }
        }

        throw new RuntimeException('Unable to determine the namespace.');
    }
}
