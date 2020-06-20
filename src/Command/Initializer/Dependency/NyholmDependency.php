<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\DependencyVersions;

/**
 * Nyholm PSR-7 Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class NyholmDependency extends Dependency
{
    public const NAME = 'Nyholm';

    /**
     * @var array<string>
     */
    protected $packages = [
        'nyholm/psr7'        => DependencyVersions::NYHOLM_PSR_7,
        'nyholm/psr7-server' => DependencyVersions::NYHOLM_PSR_7_SERVER,
    ];
}
