<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use InvalidArgumentException;

class Config {
    public const SLIM_CONSOLE_BOOTSTRAP_DIR = 'SLIM_CONSOLE_BOOTSTRAP_DIR';
    public const SLIM_CONSOLE_INDEX_DIR = 'SLIM_CONSOLE_INDEX_DIR';
    public const SLIM_CONSOLE_INDEX_FILE = 'SLIM_CONSOLE_INDEX_FILE';
    public const SLIM_CONSOLE_ROOT_DIR = 'SLIM_CONSOLE_ROOT_DIR';
    public const SLIM_CONSOLE_SOURCE_DIR = 'SLIM_CONSOLE_SOURCE_DIR';
    public const SLIM_CONSOLE_COMMANDS_DIR = 'SLIM_CONSOLE_COMMANDS_DIR';

    /**
     * @var string
     */
    protected $boostrapDir;

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
    protected $rootDir;

    /**
     * @var string
     */
    protected $sourceDir;

    /**
     * @var string|null
     */
    protected $commandsDir;

    /**
     * @param string      $boostrapDir
     * @param string      $indexDir
     * @param string      $indexFile
     * @param string      $rootDir
     * @param string      $sourceDir
     * @param string|null $commandsDir
     */
    protected function __construct(
        string $boostrapDir,
        string $indexDir,
        string $indexFile,
        string $rootDir,
        string $sourceDir,
        ?string $commandsDir = null
    ) {
        $this->boostrapDir = $boostrapDir;
        $this->indexDir = $indexDir;
        $this->indexFile = $indexFile;
        $this->rootDir = $rootDir;
        $this->sourceDir = $sourceDir;
        $this->commandsDir = $commandsDir;
    }

    /**
     * @return string
     */
    public function getBoostrapDir(): string
    {
        return $this->boostrapDir;
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
    public function getRootDir(): string
    {
        return $this->rootDir;
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
     * @return array
     */
    protected static function mergeDefaults(array $params): array
    {
        $defaults = [
            'bootstrapDir' => 'app',
            'indexDir' => 'public',
            'indexFile' => 'index.php',
            'rootDir' => null,
            'sourceDir' => 'src',
            'commandsDir' => null,
        ];

        return $defaults + $params;
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
            'rootDir' => $rootDir,
            'sourceDir' => $sourceDir,
            'commandsDir' => $commandsDir,
        ] = $params;

        $error = null;

        if (!is_string($bootstrapDir) || empty($bootstrapDir)) {
            $error = '`bootstrapDir` must be a string.';
        } elseif (!is_string($indexDir) || empty($indexDir)) {
            $error = '`indexDir` must be a string.';
        } elseif (!is_string($indexFile) || empty($indexFile)) {
            $error = '`indexFile` must be a string.';
        } elseif (!is_string($rootDir) || empty($rootDir)) {
            $error = '`rootDir` must be a string.';
        } elseif (!is_string($sourceDir) || empty($sourceDir)) {
            $error = '`sourceDir` must be a string.';
        } elseif (!empty($commandsDir) && !is_string($commandsDir)) {
            $error = '`commandsDir` must be a string.';
        }

        if ($error) {
            throw new InvalidArgumentException($error);
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
        $params = self::mergeDefaults($params);
        self::validate($params);

        return new self(
            $params['bootstrapDir'],
            $params['indexDir'],
            $params['indexFile'],
            $params['rootDir'],
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
        $params = self::mergeDefaults([
            'bootstrapDir' => getenv(self::SLIM_CONSOLE_BOOTSTRAP_DIR),
            'indexDir' => getenv(self::SLIM_CONSOLE_INDEX_DIR),
            'indexFile' => getenv(self::SLIM_CONSOLE_INDEX_FILE),
            'rootDir' => getenv(self::SLIM_CONSOLE_ROOT_DIR),
            'sourceDir' => getenv(self::SLIM_CONSOLE_SOURCE_DIR),
            'commandsDir' => getenv(self::SLIM_CONSOLE_COMMANDS_DIR),
        ]);

        self::validate($params);

        return new self(
            $params['bootstrapDir'],
            $params['indexDir'],
            $params['indexFile'],
            $params['rootDir'],
            $params['sourceDir'],
            $params['commandsDir']
        );
    }
}
