<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Exception;

use Exception;

class CannotResolveConfigException extends Exception
{
    public $message = 'Cannot resolve Slim Console configuration file.';
}
