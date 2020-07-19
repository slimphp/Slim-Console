<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\DependencyVersions;

class AuraDIDependency extends Dependency
{
    public const NAME = 'Aura.Di';

    /**
     * @var array<string>
     */
    protected $packages = [
        'aura/di' => DependencyVersions::AURA_DI,
    ];
}
