<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles;

use Exception;

/**
 * Defines version constants.
 *
 * @package Slim\Console\Command\Initializer\Profiles
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class DependencyVersions
{
    /**
     * DependencyVersions constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        throw new Exception('Class `' . __CLASS__ . '` is not instanceable.');
    }

    public const EXT_JSON = '*';
    public const PHP = '^7.2';
    public const SLIM = '^4.5';
}
