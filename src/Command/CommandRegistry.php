<?php

declare(strict_types=1);

namespace Slim\Console\Command;

use Slim\Console\Application;

class CommandRegistry
{
    public static function register(Application $app)
    {
        // Itereate through every .php file in this directory.
        foreach (glob(__DIR__ . '/*.php') as $command) {
            // Ignore CommandRegistry.php
            if (basename($command) === 'CommandRegistry.php') {
                continue;
            }

            // Determine the class name of each command class and add it
            $className = "Slim\Console\Command\\" . basename($command, '.php');
            $app->add(new $className());
        }
    }
}
