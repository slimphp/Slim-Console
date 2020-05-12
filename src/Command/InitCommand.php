<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command;

use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends AbstractCommand
{
    protected const SLIM_PROFILE_EMPTY = 'empty';

    private $availableProfiles = [
        self::SLIM_PROFILE_EMPTY,
    ];

    protected static $defaultName = 'init';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Initialize a new Slim project')
            ->addOption(
                'profile',
                'p',
                InputOption::VALUE_OPTIONAL,
                'New Slim project skeleton profile',
                self::SLIM_PROFILE_EMPTY
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profile = $input->getOption('profile');
        $useDefaultSetup = $input->getOption('default');

        if (!in_array($profile, $this->availableProfiles)) {
            throw new InvalidOptionException("Profile `{$profile}` not found!");
        }

        $output->writeln('Work In Progress!');

        return 0;
    }
}
