<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\DependencyVersions;

/**
 * Monolog PSR-3 Logging Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class MonologDependency extends Dependency
{
    public const NAME = 'Monolog';

    /**
     * @var array<string>
     */
    protected $packages = [
        'monolog/monolog' => DependencyVersions::MONOLOG,
    ];
}
