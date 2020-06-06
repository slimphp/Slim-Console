<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\Versions;

/**
 * PHP DI Dependency Container Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class PimpleDependency extends Dependency
{
    public const NAME = 'Pimple';

    /**
     * @var array<string>
     */
    protected $packages = [
        'pimple/pimple' => Versions::PIMPLE,
    ];
}
