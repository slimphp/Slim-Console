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
use Slim\Console\Config\Parser\PHPConfigParser;
use Slim\Tests\Console\TestCase;

class PHPConfigParserTest extends TestCase
{
    public function testParse(): void
    {
        $exampleJsonConfigPath = $this->getExampleConfigPath(ConfigResolver::FORMAT_PHP);
        $phpConfigPath = $exampleJsonConfigPath
            . DIRECTORY_SEPARATOR
            . ConfigResolver::CONFIG_FILENAME . '.' . ConfigResolver::FORMAT_PHP;
        $phpConfig = require $phpConfigPath;

        $config = PHPConfigParser::parse($phpConfigPath);

        $this->assertSame($phpConfig['bootstrapDir'], $config->getBootstrapDir());
        $this->assertSame($phpConfig['commandsDir'], $config->getCommandsDir());
        $this->assertSame($phpConfig['indexDir'], $config->getIndexDir());
        $this->assertSame($phpConfig['indexFile'], $config->getIndexFile());
        $this->assertSame($phpConfig['sourceDir'], $config->getSourceDir());
    }

    public function testParseThrowsInvalidArgumentException(): void
    {
        $invalidJsonConfigPath = $this->examplesConfigBasePath
            . DIRECTORY_SEPARATOR . 'invalid-php'
            . DIRECTORY_SEPARATOR . 'invalid-format.php';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Slim Console configuration should be an array.');

        PHPConfigParser::parse($invalidJsonConfigPath);
    }
}
