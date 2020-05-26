<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

use Slim\Console\Command\Initializer\Profiles\blank\Versions;

/**
 * Guzzle PSR-7 Dependency.
 *
 * @package Slim\Console\Command\Initializer\Dependency
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class GuzzleDependency extends Dependency
{
    public const NAME = 'Guzzle';

    /**
     * @var array<string>
     */
    protected $packages = [
        'guzzlehttp/psr7'                  => Versions::GIZZLE_PSR_7,
        'http-interop/http-factory-guzzle' => Versions::HTTP_FACTORY_GUZZLE,
    ];
}
