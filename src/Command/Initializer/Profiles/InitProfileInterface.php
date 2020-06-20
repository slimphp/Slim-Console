<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles;

use Slim\Console\Config\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface InitProfileInterface
{
    /**
     * InitProfileInterface constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Config|null $config  Slim Console Config object.
     */
    public function __construct(InputInterface $input, OutputInterface $output, ?Config $config = null);

    /**
     * Run new project initialization with profile.
     *
     * @param string $projectDirectory
     * @param bool $useDefaultSetup If true, use the default settings for the profile,
     * otherwise configure the project interactively.
     *
     * @return int 0 if everything went fine, or an exit code.
     */
    public function run(string $projectDirectory, bool $useDefaultSetup = false): int;
}
