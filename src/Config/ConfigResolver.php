<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config;

use InvalidArgumentException;
use ReflectionClass;
use Slim\Console\Exception\ConfigNotFoundException;

use function array_filter;
use function file_exists;
use function file_get_contents;
use function implode;
use function is_array;
use function is_null;
use function json_decode;
use function strpos;

class ConfigResolver
{
    public const FORMAT_JSON = 'json';
    public const FORMAT_PHP = 'php';
    //public const FORMAT_YAML = 'yaml';
    private const CONFIG_FILENAME = 'slim-console.config';

    /** @var string|null */
    private $file = null;

    /** @var string */
    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function loadConfig(): ?Config
    {
        $this->locateConfig();

        return $this->loadConfigByFormat();
    }

    public function getFile(): string
    {
        return $this->file ?? "";
    }

    protected function locateConfig(): void
    {
        $reflection = new ReflectionClass($this);
        $filterFormats = function ($value, $key) {
            return strpos($key, 'FORMAT') === 0;
        };
        $formats = array_filter($reflection->getConstants(), $filterFormats, ARRAY_FILTER_USE_BOTH);

        foreach ($formats as $possibleFormat) {
            $fileName = $this->dir . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME . '.' . $possibleFormat;
            if (file_exists($fileName)) {
                $this->file = $fileName;

                break;
            }
        }

        if (is_null($this->file)) {
            throw new ConfigNotFoundException(
                'Please create config file, supported formats: ' . implode(',', $formats)
            );
        }
    }

    protected function loadConfigByFormat(): ?Config
    {
        if (is_null($this->file)) {
            return null;
        }

        $format = pathinfo($this->file, PATHINFO_EXTENSION);

        if ($format === self::FORMAT_PHP) {
            $array = require($this->file);
            if (is_array($array) === false) {
                throw new InvalidArgumentException("The file $this->file should return an array");
            }

            return new Config($array, $this->dir);
        }

        if ($format === self::FORMAT_JSON) {
            $string = (string) file_get_contents($this->file);
            $array = json_decode($string, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException("The file $this->file should be a valid JSON");
            }

            return new Config($array, $this->dir);
        }

        return null;
    }
}
