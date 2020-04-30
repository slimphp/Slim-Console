<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command;

use Slim\Console\Application;
use Slim\Console\Config\Config;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class AbstractCommand extends SymfonyCommand
{
    /**
     * @return Config|null
     */
    public function getConfig(): ?Config
    {
        $app = $this->getApplication();
        if ($app instanceof Application === false) {
            return null;
        }

        return $app->getConfig();
    }

    /**
     * @param string $configDir
     */
    public function setConfig(string $configDir): void
    {
        $app = $this->getApplication();
        if ($app instanceof Application) {
            $app->setConfig($configDir);
        }
    }
}
