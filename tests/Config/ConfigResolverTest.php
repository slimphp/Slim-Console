<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Config;

use Closure;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Slim\Console\Config\Config;
use Slim\Console\Config\ConfigResolver;
use Slim\Tests\Console\TestCase;

class ConfigResolverTest extends TestCase
{
    public function supportedFormatsProvider(): array
    {
        return [
            [ConfigResolver::FORMAT_PHP, function (string $path): ?array {
                return require $path;
            }],
            [ConfigResolver::FORMAT_JSON, function (string $path): ?array {
                return json_decode(file_get_contents($path), true);
            }],
        ];
    }

    public function testResolvePrioritizesEnvironment(): void
    {
        $this->setupEnvConfig();

        $exampleJsonConfigPath = $this->getExampleConfigPath(ConfigResolver::FORMAT_JSON);
        $configResolver = new ConfigResolver();

        $config = $configResolver->resolve($exampleJsonConfigPath);

        $this->assertSame($this->envParams['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($this->envParams['commandsDir'], $config->getCommandsDir());
        $this->assertSame($this->envParams['indexDir'], $config->getIndexDir());
        $this->assertSame($this->envParams['indexFile'], $config->getIndexFile());
        $this->assertSame($this->envParams['sourceDir'], $config->getSourceDir());
    }

    /**
     * @dataProvider supportedFormatsProvider
     *
     * @param string  $format
     * @param Closure $parser
     */
    public function testAttemptResolvingConfigFromSupportedFormats(string $format, Closure $parser): void
    {
        $configPath = $this->getExampleConfigPath($format)
            . DIRECTORY_SEPARATOR
            . ConfigResolver::CONFIG_FILENAME . '.' . $format;
        $example = $parser($configPath);

        $exampleConfigPath = $this->getExampleConfigPath($format);
        $configResolver = new ConfigResolver();

        $config = $configResolver->resolve($exampleConfigPath);

        $this->assertSame($example['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($example['commandsDir'], $config->getCommandsDir());
        $this->assertSame($example['indexDir'], $config->getIndexDir());
        $this->assertSame($example['indexFile'], $config->getIndexFile());
        $this->assertSame($example['sourceDir'], $config->getSourceDir());
    }

    public function testAttemptParsingConfigFromFileThrowsRuntimeException(): void
    {
        $attemptParsingConfigFromFileMethod = new ReflectionMethod(
            ConfigResolver::class,
            'attemptParsingConfigFromFile'
        );
        $attemptParsingConfigFromFileMethod->setAccessible(true);

        $invalidFormat = 'invalid';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Invalid configuration format `{$invalidFormat}`.");

        $configResolver = new ConfigResolver();

        $attemptParsingConfigFromFileMethod->invoke($configResolver, $this->examplesConfigBasePath, $invalidFormat);
    }

    public function testResolveFallbackOnDefaults(): void
    {
        $defaultsReflection = new ReflectionProperty(Config::class, 'defaults');
        $defaultsReflection->setAccessible(true);

        $defaults = $defaultsReflection->getValue();

        $configResolver = new ConfigResolver();
        $config = $configResolver->resolve($this->examplesConfigBasePath);

        $this->assertSame($defaults['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($defaults['commandsDir'], $config->getCommandsDir());
        $this->assertSame($defaults['indexDir'], $config->getIndexDir());
        $this->assertSame($defaults['indexFile'], $config->getIndexFile());
        $this->assertSame($defaults['sourceDir'], $config->getSourceDir());
    }
}
