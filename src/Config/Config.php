<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use InvalidArgumentException;

class Config
{
    public const SLIM_CONSOLE_BOOTSTRAP_DIR = 'SLIM_CONSOLE_BOOTSTRAP_DIR';
    public const SLIM_CONSOLE_INDEX_DIR = 'SLIM_CONSOLE_INDEX_DIR';
    public const SLIM_CONSOLE_INDEX_FILE = 'SLIM_CONSOLE_INDEX_FILE';
    public const SLIM_CONSOLE_SOURCE_DIR = 'SLIM_CONSOLE_SOURCE_DIR';
    public const SLIM_CONSOLE_COMMANDS_DIR = 'SLIM_CONSOLE_COMMANDS_DIR';

    /**
     * @var array
     */
    protected static $defaults = [
        'bootstrapDir' => 'app',
        'indexDir' => 'public',
        'indexFile' => 'index.php',
        'sourceDir' => 'src',
        'commandsDir' => 'src/Application/Console/Commands',
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
     * @param array $params
     *
     * @throws InvalidArgumentException
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

        if (!is_string($bootstrapDir) || empty($bootstrapDir)) {
            throw new InvalidArgumentException('`bootstrapDir` must be a string.');
        }

        if (!is_string($indexDir) || empty($indexDir)) {
            throw new InvalidArgumentException('`indexDir` must be a string.');
        }

        if (!is_string($indexFile) || empty($indexFile)) {
            throw new InvalidArgumentException('`indexFile` must be a string.');
        }

        if (!is_string($sourceDir) || empty($sourceDir)) {
            throw new InvalidArgumentException('`sourceDir` must be a string.');
        }

        if (!empty($commandsDir) && !is_string($commandsDir)) {
            throw new InvalidArgumentException('`commandsDir` must be a string.');
        }
    }

    /**
     * @param array $params
     *
     * @return Config
     *
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $params): Config
    {
        $params = self::$defaults + $params;

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
     * @throws InvalidArgumentException
     */
    public static function fromEnvironment(): Config
    {
        return self::fromArray([
            'bootstrapDir' => getenv(self::SLIM_CONSOLE_BOOTSTRAP_DIR),
            'indexDir' => getenv(self::SLIM_CONSOLE_INDEX_DIR),
            'indexFile' => getenv(self::SLIM_CONSOLE_INDEX_FILE),
            'sourceDir' => getenv(self::SLIM_CONSOLE_SOURCE_DIR),
            'commandsDir' => getenv(self::SLIM_CONSOLE_COMMANDS_DIR),
        ]);
    }

    /**
     * @return Config
     *
     * @throws InvalidArgumentException
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
