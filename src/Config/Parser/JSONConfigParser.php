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

class JSONConfigParser extends AbstractConfigParser
{
    /**
     * @return Config
     */
    public function parse(): Config
    {
        $contents = file_get_contents($this->path);
        $parsed = json_decode($contents);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                'Invalid JSON parsed from Slim Console configuration. ' . json_last_error_msg()
            );
        } elseif (!is_array($parsed)) {
            throw new InvalidArgumentException('Slim Console configuration should be an array.');
        }

        return Config::fromArray($parsed);
    }
}
