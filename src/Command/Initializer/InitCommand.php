<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer;

use Slim\Console\Command\AbstractCommand;
use Slim\Console\Command\Initializer\Profiles\InitProfileInterface;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function basename;
use function glob;
use function in_array;
use function is_array;
use function is_string;

use const DIRECTORY_SEPARATOR;
use const GLOB_ONLYDIR;

/**
 * Class InitCommand.
 *
 * @package Slim\Console\Command\Initializer
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class InitCommand extends AbstractCommand
{
    protected const PROFILE_NAMESPACE_PREFIX = "Slim\Console\Command\Initializer\Profiles";
    protected const PROFILE_INIT_CLASS = "Init";

    protected static $defaultName = 'init';

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Initialize a new Slim project')
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'Directory of the project to create'
            )
            ->addOption(
                'profile',
                'p',
                InputOption::VALUE_REQUIRED,
                'New Slim project skeleton profile',
                'blank'
            )
            ->addOption(
                'default',
                null,
                InputOption::VALUE_NONE,
                'Use default setup'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $profileObject = null;
        $directory = $input->getArgument('directory');
        $directory = is_string($directory) ? $directory : 'new-slim-project';
        $profile = $input->getOption('profile');
        $profile = is_string($profile) ? $profile : 'blank';
        $useDefaultSetup = (bool)$input->getOption('default');

        if (!in_array($profile, $this->getAvailableProfiles())) {
            throw new InvalidOptionException("Profile `{$profile}` not found!");
        }

        $profile = self::PROFILE_NAMESPACE_PREFIX . "\\{$profile}\\" . self::PROFILE_INIT_CLASS;
        /** @var InitProfileInterface $profileObject */
        $profileObject = new $profile($input, $output, $this->getConfig());

        return $profileObject->run($directory, $useDefaultSetup);
    }

    /**
     * Get available initialization profiles.
     *
     * @return array<string>
     */
    private function getAvailableProfiles(): array
    {
        $profiles = [];
        $glob = glob(__DIR__ . DIRECTORY_SEPARATOR . 'Profiles' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

        if (is_array($glob)) {
            foreach ($glob as $profile) {
                $profiles[] = basename($profile);
            }
        }

        return $profiles;
    }
}
