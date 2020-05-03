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

interface ConfigParserInterface
{
    /**
     * @return Config
     *
     * @throws InvalidArgumentException
     */
    public function parse(): Config;
}
