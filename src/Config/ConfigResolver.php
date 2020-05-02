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
     * @throws CannotResolveConfigurationException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function resolve(): Config
    {
        try {
            return Config::fromEnvironment();
        } catch (InvalidArgumentException $e) {
            return $this->attemptResolvingConfigurationFromSupportedFormats();
        }
    }

    /**
     * @return Config
     *
     * @throws CannotResolveConfigurationException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function attemptResolvingConfigurationFromSupportedFormats(): Config
    {
        foreach ($this->supportedFormats as $format) {
            $path = $this->rootDir . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME . ".{$format}";
            if (file_exists($path)) {
                return $this->attemptParsingConfigurationFromFile($path, $format);
            }
        }

        throw new CannotResolveConfigurationException();
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
    protected function attemptParsingConfigurationFromFile(string $path, string $format): Config
    {
        switch ($format) {
            case self::FORMAT_PHP:
                $parsed = require_once $path;

                if (!is_array($parsed)) {
                    throw new InvalidArgumentException('Slim Console configuration should be an array.');
                }

                return Config::fromArray($parsed);

            case self::FORMAT_JSON:
                $contents = file_get_contents($path);
                $parsed = json_decode($contents);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new InvalidArgumentException('Invalid JSON parsed from Slim Console configuration.');
                } elseif (!is_array($parsed)) {
                    throw new InvalidArgumentException('Slim Console configuration should be an array.');
                }

                return Config::fromArray($parsed);

            default:
                throw new RuntimeException("Invalid configuration format `{$format}`.");
        }
    }
}
