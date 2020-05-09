<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Command;

use RuntimeException;
use Slim\Console\Config\Config;
use Slim\Console\App;
use Slim\Tests\Console\Mocks\MockCommand;
use Slim\Tests\Console\TestCase;

class AbstractCommandTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = Config::fromDefaults();

        $mockCommand = new MockCommand();

        $app = new App($config);
        $app->add($mockCommand);

        $this->assertSame($config, $mockCommand->getConfig());
    }

    public function testGetConfigThrowsRuntimeExceptionWithIncompatibleApp(): void
    {
        $mockCommand = new MockCommand();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method method `getConfig()` does not exist on this type of application.');

        $mockCommand->getConfig();
    }
}
