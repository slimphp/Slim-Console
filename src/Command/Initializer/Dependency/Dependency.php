<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

namespace Slim\Console\Command\Initializer\Dependency;

abstract class Dependency
{
    public const NAME = 'Abstract Dependency';

    /**
     * @var array<string>
     */
    protected $packages = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @return array<string>
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
