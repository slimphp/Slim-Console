<?php

declare(strict_types=1);

namespace Slim\Tests\Console;

use PHPUnit\Framework\TestCase;
use Slim\Console\Application;
use Slim\Console\Config\Config;

class ApplicationTest extends TestCase
{
    public function testShouldRunApplication(): void
    {
        $configDir = __DIR__ . DIRECTORY_SEPARATOR . 'ExampleConfig' . DIRECTORY_SEPARATOR . 'Php';
        $app = new Application($configDir);

        $this->assertInstanceOf(Config::class, $app->getConfig());
    }
}
