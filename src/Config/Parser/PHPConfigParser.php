<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Config\Parser;

use Slim\Console\Config\Config;
use Slim\Console\Exception\CannotParseConfigException;

use function is_array;

class PHPConfigParser implements ConfigParserInterface
{
    /**
     * {@inheritdoc}
     */
    public static function parse(string $path): Config
    {
        $parsed = require $path;

        if (!is_array($parsed)) {
            throw new CannotParseConfigException('Slim Console configuration should be an array.');
        }

        return Config::fromArray($parsed);
    }
}
