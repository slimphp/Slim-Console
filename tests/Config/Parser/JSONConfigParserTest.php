<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console\Config\Parser;

use InvalidArgumentException;
use Slim\Console\Config\ConfigResolver;
use Slim\Console\Config\Parser\JSONConfigParser;
use Slim\Tests\Console\TestCase;

class JSONConfigParserTest extends TestCase
{
    public function invalidConfigurationProvider(): array
    {
        return [
          ['invalid-format.json', 'Slim Console configuration should be an array.'],
          ['invalid-syntax.json', 'Invalid JSON parsed from Slim Console configuration. Syntax error'],
        ];
    }

    public function testParse(): void
    {
        $exampleJsonConfigPath = $this->getExampleConfigPath(ConfigResolver::FORMAT_JSON);
        $jsonConfigPath = $exampleJsonConfigPath . DIRECTORY_SEPARATOR . ConfigResolver::CONFIG_FILENAME . '.' . ConfigResolver::FORMAT_JSON;
        $jsonConfig = json_decode(file_get_contents($jsonConfigPath), true);

        $config = JSONConfigParser::parse($jsonConfigPath);

        $this->assertSame($jsonConfig['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($jsonConfig['commandsDir'], $config->getCommandsDir());
        $this->assertSame($jsonConfig['indexDir'], $config->getIndexDir());
        $this->assertSame($jsonConfig['indexFile'], $config->getIndexFile());
        $this->assertSame($jsonConfig['sourceDir'], $config->getSourceDir());
    }

    /**
     * @dataProvider invalidConfigurationProvider
     *
     * @param string $fileName
     * @param string $expectedExceptionMessage
     */
    public function testParseThrowsInvalidArgumentException(string $fileName, string $expectedExceptionMessage): void
    {
        $invalidJsonConfigPath = $this->examplesConfigBasePath . DIRECTORY_SEPARATOR . 'invalid-json' . DIRECTORY_SEPARATOR . $fileName;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        JSONConfigParser::parse($invalidJsonConfigPath);
    }
}
