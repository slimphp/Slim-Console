<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\Versions;

/**
 * Slim PSR-7 Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class SlimPsr7Dependency extends Dependency
{
    public const NAME = 'Slim PSR-7';

    /**
     * @var array<string>
     */
    protected $packages = [
        'slim/psr7' => Versions::SLIM_PSR_7,
    ];
}
