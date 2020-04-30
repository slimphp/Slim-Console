<?php

declare(strict_types=1);

namespace Slim\Tests\Console\Command;

use PHPUnit\Framework\TestCase;
use Slim\Console\Application;
use Slim\Console\Config\Config;

class ExampleCommandTest extends TestCase
{
    public function testShouldLoadWithAccessToConfig(): void
    {
        $configDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ExampleConfig' . DIRECTORY_SEPARATOR . 'Php';
        $command = new ExampleCommand();
        $command->setApplication(new Application($configDir));

        $this->assertInstanceOf(Config::class, $command->getConfig());
    }
}
