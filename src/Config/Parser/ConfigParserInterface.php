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

interface ConfigParserInterface
{
    /**
     * @param string $path
     *
     * @return Config
     *
     * @throws CannotParseConfigException
     */
    public static function parse(string $path): Config;
}
