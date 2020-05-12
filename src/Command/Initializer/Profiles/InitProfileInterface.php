<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface InitProfileInterface
 *
 * @package Slim\Console\Command\Initializer\Profiles
 * @author Temuri Takalandze <me@abgeo.dev>
 */
interface InitProfileInterface
{
    /**
     * Run new project initialization with profile.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param bool $useDefaultSetup If true, use the default settings for the profile,
     * otherwise configure the project interactively.
     *
     * @return int 0 if everything went fine, or an exit code.
     */
    public function run(InputInterface $input, OutputInterface $output, bool $useDefaultSetup = false): int;
}
