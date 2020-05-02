<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command;

use RuntimeException;
use Slim\Console\App;
use Slim\Console\Config\Config;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    /**
     * @return Config|null
     */
    public function getConfig(): ?Config
    {
        $app = $this->getApplication();

        if ($app instanceof App === false) {
            throw new RuntimeException('Cannot call method `getConfig()` on ' . get_class($app));
        }

        return $app->getConfig();
    }
}
