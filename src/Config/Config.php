<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use Slim\Console\Exception\ConfigValidationException;

use function array_merge;
use function ctype_space;
use function getenv;
use function is_string;

use const DIRECTORY_SEPARATOR;

class Config
{
    public const SLIM_CONSOLE_BOOTSTRAP_DIR = 'SLIM_CONSOLE_BOOTSTRAP_DIR';
    public const SLIM_CONSOLE_INDEX_DIR = 'SLIM_CONSOLE_INDEX_DIR';
    public const SLIM_CONSOLE_INDEX_FILE = 'SLIM_CONSOLE_INDEX_FILE';
    public const SLIM_CONSOLE_SOURCE_DIR = 'SLIM_CONSOLE_SOURCE_DIR';
    public const SLIM_CONSOLE_COMMANDS_DIR = 'SLIM_CONSOLE_COMMANDS_DIR';

    /**
     * @var array<string, string>
     */
    protected static $defaults = [
        'bootstrapDir' => 'app',
        'indexDir' => 'public',
        'indexFile' => 'index.php',
        'sourceDir' => 'src',
        'commandsDir' => 'src'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Console'
            . DIRECTORY_SEPARATOR
            . 'Commands',
    ];

    /**
     * @var string
     */
    protected $bootstrapDir;

    /**
     * @var string
     */
    protected $indexDir;

    /**
     * @var string
     */
    protected $indexFile;

    /**
     * @var string
     */
    protected $sourceDir;

    /**
     * @var string|null
     */
    protected $commandsDir;

    /**
     * @param string      $bootstrapDir
     * @param string      $indexDir
     * @param string      $indexFile
     * @param string      $sourceDir
     * @param string|null $commandsDir
     */
    protected function __construct(
        string $bootstrapDir,
        string $indexDir,
        string $indexFile,
        string $sourceDir,
        ?string $commandsDir = null
    ) {
        $this->bootstrapDir = $bootstrapDir;
        $this->indexDir = $indexDir;
        $this->indexFile = $indexFile;
        $this->sourceDir = $sourceDir;
        $this->commandsDir = $commandsDir;
    }

    /**
     * @return string
     */
    public function getBootstrapDir(): string
    {
        return $this->bootstrapDir;
    }

    /**
     * @return string
     */
    public function getIndexDir(): string
    {
        return $this->indexDir;
    }

    /**
     * @return string
     */
    public function getIndexFile(): string
    {
        return $this->indexFile;
    }

    /**
     * @return string
     */
    public function getSourceDir(): string
    {
        return $this->sourceDir;
    }

    /**
     * @return string|null
     */
    public function getCommandsDir(): ?string
    {
        return $this->commandsDir;
    }

    /**
     * @param array<string, string> $params
     *
     * @throws ConfigValidationException
     */
    protected static function validate(array $params): void
    {
        [
            'bootstrapDir' => $bootstrapDir,
            'indexDir' => $indexDir,
            'indexFile' => $indexFile,
            'sourceDir' => $sourceDir,
            'commandsDir' => $commandsDir,
        ] = $params;

        if (!is_string($bootstrapDir) || empty($bootstrapDir) || ctype_space($bootstrapDir)) {
            throw new ConfigValidationException('`bootstrapDir` must be a string.');
        }

        if (!is_string($indexDir) || empty($indexDir) || ctype_space($indexDir)) {
            throw new ConfigValidationException('`indexDir` must be a string.');
        }

        if (!is_string($indexFile) || empty($indexFile) || ctype_space($indexFile)) {
            throw new ConfigValidationException('`indexFile` must be a string.');
        }

        if (!is_string($sourceDir) || empty($sourceDir) || ctype_space($sourceDir)) {
            throw new ConfigValidationException('`sourceDir` must be a string.');
        }

        if (!empty($commandsDir) && (!is_string($commandsDir) || ctype_space($commandsDir))) {
            throw new ConfigValidationException('`commandsDir` must be a string.');
        }
    }

    /**
     * @param array<string, string> $params
     *
     * @return Config
     *
     * @throws ConfigValidationException
     */
    public static function fromArray(array $params): Config
    {
        $params = array_merge(self::$defaults, $params);

        self::validate($params);

        return new self(
            $params['bootstrapDir'],
            $params['indexDir'],
            $params['indexFile'],
            $params['sourceDir'],
            $params['commandsDir']
        );
    }

    /**
     * @return Config
     *
     * @throws ConfigValidationException
     */
    public static function fromEnvironment(): Config
    {
        return self::fromArray([
            'bootstrapDir' => (string) getenv(self::SLIM_CONSOLE_BOOTSTRAP_DIR),
            'indexDir' => (string) getenv(self::SLIM_CONSOLE_INDEX_DIR),
            'indexFile' => (string) getenv(self::SLIM_CONSOLE_INDEX_FILE),
            'sourceDir' => (string) getenv(self::SLIM_CONSOLE_SOURCE_DIR),
            'commandsDir' => (string) getenv(self::SLIM_CONSOLE_COMMANDS_DIR),
        ]);
    }

    /**
     * @return Config
     */
    public static function fromDefaults(): Config
    {
        return new self(
            self::$defaults['bootstrapDir'],
            self::$defaults['indexDir'],
            self::$defaults['indexFile'],
            self::$defaults['sourceDir'],
            self::$defaults['commandsDir']
        );
    }
}
