<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

/**
 * Other/Empty Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class OtherDependency extends Dependency
{
    public const NAME = 'Other';

    /**
     * @var array<string>
     */
    protected $packages = [];

    /**
     * Add to packages.
     *
     * @param string $package Package Name (vendor/name).
     * @param string $version Package Version.
     */
    public function addPackage(string $package, string $version): void
    {
        $this->packages[$package] = $version;
    }
}
