<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Slim\Console\Config\Config;

use function putenv;

use const DIRECTORY_SEPARATOR;

abstract class TestCase extends PHPUnitTestCase
{
    public function setUp(): void
    {
        putenv(Config::SLIM_CONSOLE_BOOTSTRAP_DIR . '=');
        putenv(Config::SLIM_CONSOLE_COMMANDS_DIR . '=');
        putenv(Config::SLIM_CONSOLE_INDEX_DIR . '=');
        putenv(Config::SLIM_CONSOLE_INDEX_FILE . '=');
        putenv(Config::SLIM_CONSOLE_SOURCE_DIR . '=');
    }

    /**
     * @var string
     */
    protected $examplesConfigBasePath = __DIR__ . DIRECTORY_SEPARATOR . 'examples';

    /**
     * @param string $format
     *
     * @return string
     */
    protected function getExampleConfigPath(string $format): string
    {
        return $this->examplesConfigBasePath . DIRECTORY_SEPARATOR . $format;
    }

    /**
     * @var string[]
     */
    protected $envParams = [
        'bootstrapDir' => 'envCustomDir',
        'commandsDir' => 'envCustomCommands',
        'indexDir' => 'envCustomIndexDir',
        'indexFile' => 'envCustom.php',
        'sourceDir' => 'envCustomSrc',
    ];

    protected function setupEnvConfig(): void
    {
        putenv(Config::SLIM_CONSOLE_BOOTSTRAP_DIR . '=' . $this->envParams['bootstrapDir']);
        putenv(Config::SLIM_CONSOLE_COMMANDS_DIR . '=' . $this->envParams['commandsDir']);
        putenv(Config::SLIM_CONSOLE_INDEX_DIR . '=' . $this->envParams['indexDir']);
        putenv(Config::SLIM_CONSOLE_INDEX_FILE . '=' . $this->envParams['indexFile']);
        putenv(Config::SLIM_CONSOLE_SOURCE_DIR . '=' . $this->envParams['sourceDir']);
    }
}
