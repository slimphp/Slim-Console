<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Config;

use Slim\Console\Config\Config;
use PHPUnit\Framework\TestCase;
use Slim\Console\Config\ConfigResolver;
use Slim\Console\Exception\ConfigNotFoundException;

use function dirname;
use function pathinfo;

class ConfigResolverTest extends TestCase
{
    public function testShouldResolveJsonConfig(): void
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ExampleConfig' . DIRECTORY_SEPARATOR . 'Json';
        $resolver = new ConfigResolver($path);

        $this->assertInstanceOf(Config::class, $resolver->loadConfig());
        $this->assertEquals(pathinfo($resolver->getFile(), PATHINFO_EXTENSION), ConfigResolver::FORMAT_JSON);
    }

    public function testShouldResolvePhpConfig(): void
    {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ExampleConfig' . DIRECTORY_SEPARATOR . 'Php';
        $resolver = new ConfigResolver($path);

        $this->assertInstanceOf(Config::class, $resolver->loadConfig());
        $this->assertEquals(pathinfo($resolver->getFile(), PATHINFO_EXTENSION), ConfigResolver::FORMAT_PHP);
    }

    public function testShouldThrowExceptionIfConfigCannotBeFound(): void
    {
        $path = 'Wrong/Path';
        $resolver = new ConfigResolver($path);

        $this->expectException(ConfigNotFoundException::class);
        $resolver->loadConfig();
    }
}
