<?php

namespace Test\Essential\Core;

use PHPUnit\Framework\TestCase;
use Test\Essential\Core\Package\MyPackage;

class PackageInterfaceTest extends TestCase
{
    public function test()
    {
        $package = new MyPackage();
        $definitions = $package->getDefinitions();
        $this->assertIsArray($package->getDefinitions());

        // Assert that the parameters are of type array
        $this->assertIsArray($package->getParameters());

        // Assert that the routes are of type array
        $this->assertIsArray($package->getRoutes());

        // Assert that the listeners are of type array
        $this->assertIsArray($package->getListeners());

        // Assert that the commands are of type array
        $this->assertIsArray($package->getCommands());
    }
}