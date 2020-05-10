<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Config;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use Slim\Console\Config\Config;
use Slim\Tests\Console\TestCase;

use function array_merge;

class ConfigTest extends TestCase
{
    public function testFromArray(): void
    {
        $bootstrapDir = 'customDir';
        $commandsDir = 'customCommands';
        $indexDir = 'customIndexDir';
        $indexFile = 'custom.php';
        $sourceDir = 'customSrc';

        $config = Config::fromArray([
            'bootstrapDir' => $bootstrapDir,
            'commandsDir' => $commandsDir,
            'indexDir' => $indexDir,
            'indexFile' => $indexFile,
            'sourceDir' => $sourceDir,
        ]);

        $this->assertSame($bootstrapDir, $config->getBootstrapDir());
        $this->assertSame($commandsDir, $config->getCommandsDir());
        $this->assertSame($indexDir, $config->getIndexDir());
        $this->assertSame($indexFile, $config->getIndexFile());
        $this->assertSame($sourceDir, $config->getSourceDir());
    }

    public function testFromEnvironment(): void
    {
        $this->setupEnvConfig();

        $config = Config::fromEnvironment();

        $this->assertSame($this->envParams['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($this->envParams['commandsDir'], $config->getCommandsDir());
        $this->assertSame($this->envParams['indexDir'], $config->getIndexDir());
        $this->assertSame($this->envParams['indexFile'], $config->getIndexFile());
        $this->assertSame($this->envParams['sourceDir'], $config->getSourceDir());
    }

    public function testFromDefaults(): void
    {
        $defaultsReflection = new ReflectionProperty(Config::class, 'defaults');
        $defaultsReflection->setAccessible(true);

        $defaults = $defaultsReflection->getValue();
        $config = Config::fromDefaults();

        $this->assertSame($defaults['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($defaults['commandsDir'], $config->getCommandsDir());
        $this->assertSame($defaults['indexDir'], $config->getIndexDir());
        $this->assertSame($defaults['indexFile'], $config->getIndexFile());
        $this->assertSame($defaults['sourceDir'], $config->getSourceDir());
    }

    public function invalidDataProvider(): array
    {
        return [
            ['bootstrapDir', ''],
            ['bootstrapDir', ' '],
            ['commandsDir', ' '],
            ['indexDir', ''],
            ['indexDir', ' '],
            ['indexFile', ''],
            ['indexFile', ' '],
            ['sourceDir', ''],
            ['sourceDir', ' '],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param string $param
     * @param string $value
     */
    public function testValidate(string $param, string $value): void
    {
        $configReflection = new ReflectionClass(Config::class);

        $defaultsReflection = $configReflection->getProperty('defaults');
        $defaultsReflection->setAccessible(true);

        $defaults = $defaultsReflection->getValue();

        $validateMethod = $configReflection->getMethod('validate');
        $validateMethod->setAccessible(true);

        $params = array_merge($defaults, [$param => $value]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("`{$param}` must be a string.");

        $validateMethod->invoke(null, $params);
    }
}
