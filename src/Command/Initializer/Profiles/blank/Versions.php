<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles\blank;

use Slim\Console\Command\Initializer\Profiles\Versions as ParentVersions;

/**
 * Defines version constants.
 *
 * @package Slim\Console\Command\Initializer\Profiles\blank
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class Versions extends ParentVersions
{
    public const PHP_UNIT = '^8.5';
    public const SLIM_PSR_7 = '^1.1';
    public const LAMINAS = '^2.3';
    public const GIZZLE_PSR_7 = '^1.6';
    public const HTTP_FACTORY_GUZZLE = '^1.0';
    public const NYHOLM_PSR_7 = '^1.2';
    public const NYHOLM_PSR_7_SERVER = '^0.4';
    public const PHP_DI = '^6.1';
    public const PIMPIE = '^3.0';
    public const MONOLOG = '^2.0';
}
