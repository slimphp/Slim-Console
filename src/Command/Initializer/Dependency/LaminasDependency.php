<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\DependencyVersions;

/**
 * Laminas Diactoros PSR-7 Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class LaminasDependency extends Dependency
{
    public const NAME = 'Laminas';

    /**
     * @var array<string>
     */
    protected $packages = [
        'laminas/laminas-diactoros' => DependencyVersions::LAMINAS,
    ];
}
