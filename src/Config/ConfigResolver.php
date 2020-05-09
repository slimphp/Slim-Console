<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use RuntimeException;
use Slim\Console\Config\Parser\JSONConfigParser;
use Slim\Console\Config\Parser\PHPConfigParser;
use Slim\Console\Exception\CannotParseConfigException;
use Slim\Console\Exception\CannotResolveConfigException;

class ConfigResolver
{
    public const CONFIG_FILENAME = 'slim-console.config';

    public const FORMAT_PHP = 'php';

    public const FORMAT_JSON = 'json';

    /**
     * @var string[]
     */
    protected $supportedFormats = [
        self::FORMAT_PHP,
        self::FORMAT_JSON,
    ];

    /**
     * Resolve configuration. Environment takes precedence over configuration file.
     *
     * @param string|null $dir
     *
     * @return Config
     *
     * @throws CannotParseConfigException
     * @throws RuntimeException
     */
    public function resolve(string $dir = null): Config
    {
        try {
            return $this->attemptResolvingConfigFromEnvironment();
        } catch (CannotResolveConfigException $e) {
            try {
                return $this->attemptResolvingConfigFromSupportedFormats($dir);
            } catch (CannotResolveConfigException $e) {
                return Config::fromDefaults();
            }
        }
    }

    /**
     * @return Config
     *
     * @throws CannotResolveConfigException
     */
    protected function attemptResolvingConfigFromEnvironment(): Config
    {
        $bootstrapDir = getenv(Config::SLIM_CONSOLE_BOOTSTRAP_DIR);
        $commandsDir = getenv(Config::SLIM_CONSOLE_COMMANDS_DIR);
        $indexDir = getenv(Config::SLIM_CONSOLE_INDEX_DIR);
        $indexFile = getenv(Config::SLIM_CONSOLE_INDEX_FILE);
        $sourceDir = getenv(Config::SLIM_CONSOLE_SOURCE_DIR);

        if (
            (is_string($bootstrapDir) && !empty($bootstrapDir) && !ctype_space($bootstrapDir))
            || (is_string($commandsDir) && !empty($commandsDir) && !ctype_space($commandsDir))
            || (is_string($indexDir) && !empty($indexDir) && !ctype_space($indexDir))
            || (is_string($indexFile) && !empty($indexFile) && !ctype_space($indexFile))
            || (is_string($sourceDir) && !empty($sourceDir) && !ctype_space($sourceDir))
        ) {
            return Config::fromEnvironment();
        }

        throw new CannotResolveConfigException();
    }

    /**
     * @param string|null $dir
     *
     * @return Config
     *
     * @throws CannotResolveConfigException
     * @throws CannotParseConfigException
     * @throws RuntimeException
     */
    protected function attemptResolvingConfigFromSupportedFormats(string $dir = null): Config
    {
        $dir = $dir ?? getcwd();
        $basePath = $dir . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

        foreach ($this->supportedFormats as $format) {
            $path = $basePath . ".{$format}";
            if (is_file($path) && is_readable($path)) {
                return $this->attemptParsingConfigFromFile($path, $format);
            }
        }

        throw new CannotResolveConfigException();
    }

    /**
     * @param string $path
     * @param string $format
     *
     * @return Config
     *
     * @throws CannotParseConfigException
     * @throws RuntimeException
     */
    protected function attemptParsingConfigFromFile(string $path, string $format): Config
    {
        switch ($format) {
            case self::FORMAT_PHP:
                return PHPConfigParser::parse($path);

            case self::FORMAT_JSON:
                return JSONConfigParser::parse($path);

            default:
                throw new RuntimeException("Invalid configuration format `{$format}`.");
        }
    }
}
