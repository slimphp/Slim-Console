<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\DependencyVersions;

class PHPDIDependency extends Dependency
{
    public const NAME = 'PHP DI';

    /**
     * @var array<string>
     */
    protected $packages = [
        'php-di/php-di' => DependencyVersions::PHP_DI,
    ];
}
