<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config\Parser;

use InvalidArgumentException;
use Slim\Console\Config\Config;

class PHPConfigParser implements ConfigParserInterface
{
    /**
     * @param string $path
     *
     * @return Config
     */
    public static function parse(string $path): Config
    {
        $parsed = require $path;

        if (!is_array($parsed)) {
            throw new InvalidArgumentException('Slim Console configuration should be an array.');
        }

        return Config::fromArray($parsed);
    }
}
