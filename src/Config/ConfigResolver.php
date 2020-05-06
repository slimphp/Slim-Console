<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use InvalidArgumentException;
use RuntimeException;
use Slim\Console\Config\Parser\JSONConfigParser;
use Slim\Console\Config\Parser\PHPConfigParser;
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
     * @var string
     */
    protected $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Resolve configuration. Environment takes precedence over configuration file.
     *
     * @return Config
     *
     * @throws CannotResolveConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function resolve(): Config
    {
        try {
            return Config::fromEnvironment();
        } catch (InvalidArgumentException $e) {
            return $this->attemptResolvingConfigFromSupportedFormats();
        }
    }

    /**
     * @return Config
     *
     * @throws CannotResolveConfigException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function attemptResolvingConfigFromSupportedFormats(): Config
    {
        $basePath = $this->rootDir . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

        foreach ($this->supportedFormats as $format) {
            $path = $basePath . ".{$format}";
            if (file_exists($path)) {
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
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function attemptParsingConfigFromFile(string $path, string $format): Config
    {
        switch ($format) {
            case self::FORMAT_PHP:
                return (new PHPConfigParser($path))->parse();

            case self::FORMAT_JSON:
                return (new JSONConfigParser($path))->parse();

            default:
                throw new RuntimeException("Invalid configuration format `{$format}`.");
        }
    }
}
