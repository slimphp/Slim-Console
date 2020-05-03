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

class PHPConfigParser extends AbstractConfigParser
{
    /**
     * @return Config
     */
    public function parse(): Config
    {
        $parsed = require $this->path;

        if (!is_array($parsed)) {
            throw new InvalidArgumentException('Slim Console configuration should be an array.');
        }

        return Config::fromArray($parsed);
    }
}
