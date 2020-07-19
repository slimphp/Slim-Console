<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles\blank;

use Slim\Console\Command\Initializer\Profiles\DependencyVersions as ParentVersions;

class DependencyVersions extends ParentVersions
{
    public const AURA_DI = '^4.1';
    public const GUZZLE_PSR_7 = '^1.6';
    public const HTTP_FACTORY_GUZZLE = '^1.0';
    public const LAMINAS_DIACTOROS = '^2.3';
    public const LEAGUE_CONTAINER = '^3.3';
    public const MONOLOG = '^2.0';
    public const NYHOLM_PSR_7 = '^1.2';
    public const NYHOLM_PSR_7_SERVER = '^0.4';
    public const PHP_DI = '^6.1';
    public const PHP_UNIT = '^8.5';
    public const PIMPLE = '^3.0';
    public const SLIM_PSR_7 = '^1.1';
}
