<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles\blank;

use Slim\Console\Command\Initializer\Profiles\AbstractInitProfile;
use Slim\Console\Config\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function copy;
use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function is_dir;
use function is_file;
use function mkdir;
use function str_replace;
use function touch;

/**
 * Init class implementation for profile Blank.
 *
 * @package Slim\Console\Command\Initializer\Profiles\blank
 * @author Temuri Takalandze <me@abgeo.dev>
 */
class Init extends AbstractInitProfile
{
    /**
     * @var string
     */
    private $templatesDirectory;

    public function __construct(InputInterface $input, OutputInterface $output, ?Config $config = null)
    {
        parent::__construct($input, $output, $config);

        $this->templatesDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'templates';
    }

    /**
     * {@inheritDoc}
     */
    public function run(string $projectDirectory, bool $useDefaultSetup = false): int
    {
        $exitCode = null;

        if (0 !== ($exitCode = parent::run($projectDirectory, $useDefaultSetup))) {
            return $exitCode;
        }

        if (0 !== ($exitCode = $this->createStructure($projectDirectory))) {
            return $exitCode;
        }

        if (0 !== ($exitCode = $this->setupDependencies($projectDirectory))) {
            return $exitCode;
        }

        $this->io->warning('Work In Progress!');

        return 0;
    }

    /**
     * Create basic directory and files structure.
     *
     * @param string $projectDirectory
     *
     * @return int The Exit Code.
     */
    protected function createStructure(string $projectDirectory): int
    {
        $phpunitTemplate = null;
        $directoryFullPath = getcwd() . DIRECTORY_SEPARATOR . $projectDirectory;
        $composerJsonContent = $this->readComposerJson($directoryFullPath);
        $directoriesToCreate = [
            'bootstrap' => $this->config ? $this->config->getBootstrapDir() : 'app',
            'index'     => $this->config ? $this->config->getIndexDir() : 'public',
            'source'    => $this->config ? $this->config->getSourceDir() : 'src',
            'logs'      => 'logs',
            'tests'     => 'tests',
        ];
        $filesToCreate = [
            $directoriesToCreate['bootstrap'] . DIRECTORY_SEPARATOR . 'dependencies.php',
            $directoriesToCreate['bootstrap'] . DIRECTORY_SEPARATOR . 'middleware.php',
            $directoriesToCreate['bootstrap'] . DIRECTORY_SEPARATOR . 'routes.php',
            $directoriesToCreate['bootstrap'] . DIRECTORY_SEPARATOR . 'settings.php',

            $directoriesToCreate['index'] . DIRECTORY_SEPARATOR .
                ($this->config ? $this->config->getIndexFile() : 'index.php'),
        ];

        foreach ($directoriesToCreate as $directory) {
            if (!is_dir($directoryFullPath . DIRECTORY_SEPARATOR . $directory)) {
                mkdir($directoryFullPath . DIRECTORY_SEPARATOR . $directory, 0755, true);
            }
        }

        foreach ($filesToCreate as $file) {
            if (!is_file($directoryFullPath . DIRECTORY_SEPARATOR . $file)) {
                touch($directoryFullPath . DIRECTORY_SEPARATOR . $file);
            }
        }

        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . '.gitignore.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . '.gitignore'
        );

        // Setup PHPUnit.

        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php'
        );

        $phpunitTemplate = file_get_contents($this->templatesDirectory . DIRECTORY_SEPARATOR . 'phpunit.xml.template');
        $phpunitTemplate = str_replace(
            ['{testsDirectory}', '{sourceDirectory}'],
            [
                '.' . DIRECTORY_SEPARATOR . ($this->config ? $this->config->getSourceDir() : 'src')
                . DIRECTORY_SEPARATOR,
                '.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR,
            ],
            $phpunitTemplate ? $phpunitTemplate : ''
        );
        file_put_contents($directoryFullPath . DIRECTORY_SEPARATOR . 'phpunit.xml', $phpunitTemplate);

        $composerJsonContent['require-dev']['phpunit/phpunit'] = Versions::PHP_UNIT;
        $composerJsonContent['scripts']['test'] = 'phpunit';
        $composerJsonContent['autoload-dev'] = [
            'psr-4' => [
                'Tests\\' => 'tests',
            ],
        ];

        // End of Setup PHPUnit.

        return $this->writeToComposerJson($directoryFullPath, $composerJsonContent);
    }

    /**
     * Setup project dependencies.
     *
     * @param string $projectDirectory
     *
     * @return int The Exit Code.
     */
    protected function setupDependencies(string $projectDirectory): int
    {
        $settingsTemplate = null;
        $directoryFullPath = getcwd() . DIRECTORY_SEPARATOR . $projectDirectory;
        $composerJsonContent = $this->readComposerJson($directoryFullPath);
        $bootstrapDirectory = $this->config ? $this->config->getBootstrapDir() : 'app';
        $indexDirectory = $this->config ? $this->config->getIndexDir() : 'public';
        $dependencies = $this->askDependencies();

        foreach ($dependencies as $dependency) {
            $composerJsonContent['require'][$dependency['package']] = $dependency['version'];
        }

        // TODO: Setup from $dependencies.
        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR
            . 'dependencies.php.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR . 'dependencies.php'
        );
        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR
            . 'middleware.php.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR . 'middleware.php'
        );
        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR
            . 'routes.php.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR . 'routes.php'
        );

        $settingsTemplate = file_get_contents(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR
            . 'settings.php.template'
        );
        $settingsTemplate = str_replace('{appName}', $projectDirectory, $settingsTemplate ? $settingsTemplate : '');
        file_put_contents(
            $directoryFullPath . DIRECTORY_SEPARATOR . $bootstrapDirectory . DIRECTORY_SEPARATOR . 'settings.php',
            $settingsTemplate
        );

        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . $indexDirectory . DIRECTORY_SEPARATOR
            . 'index.php.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . $indexDirectory . DIRECTORY_SEPARATOR . 'index.php'
        );

        return $this->writeToComposerJson($directoryFullPath, $composerJsonContent);
    }

    /**
     * Collect dependencies from user input.
     *
     * @return array<array>
     */
    protected function askDependencies(): array
    {
        $requestResponse = null;
        $dependencyContainer = null;
        $logger = null;
        $availableDependencies = [
            'requestResponse' => [
                'Slim PSR-7' => [
                    'id'      => 'slim_psr_7',
                    'package' => 'slim/psr7',
                    'version' => Versions::SLIM_PSR_7,
                ],
                'Laminas' => [
                    'id'      => 'laminas',
                    'package' => 'laminas/laminas-diactoros',
                    'version' => Versions::LAMINAS,
                ],
                'Guzzle' => [
                    'id'      => 'guzzle',
                    'package' => 'guzzlehttp/psr7',
                    'version' => Versions::GIZZLE_PSR_7,
                ],
                'Nyholm' => [
                    'id'      => 'nyholm',
                    'package' => 'nyholm/psr7',
                    'version' => Versions::NHYOLM_PSR_7,
                ],
            ],
            'dependencyContainer' => [
                'PHP DI' => [
                    'id'      => 'php_di',
                    'package' => 'php-di/php-di',
                    'version' => Versions::PHP_DI,
                ],
                'Pimple' => [
                    'id'      => 'pimple',
                    'package' => 'pimple/pimple',
                    'version' => Versions::PIMPIE,
                ],
                'Other' => [
                    'id'      => 'other',
                    'package' => null,
                    'version' => null,
                ],
            ],
            'logger' => [
                'Monolog' => [
                    'id'      => 'monolog',
                    'package' => 'monolog/monolog',
                    'version' => Versions::MONOLOG,
                ],
            ],
        ];
        $dependencies = [];

        if ($this->io->confirm('Do you want to configure the PSR-7 HTTP message interface?')) {
            $requestResponse = $this->io->choice(
                'Select PSR-7 implementation',
                array_keys($availableDependencies['requestResponse']),
                'Slim PSR-7'
            );

            $dependencies['requestResponse'] = $availableDependencies['requestResponse'][$requestResponse];
        }

        if ($this->io->confirm('Do you want to configure Dependency Container?')) {
            $dependencyContainer = $this->io->choice(
                'Select Dependency Container',
                array_keys($availableDependencies['dependencyContainer']),
                'PHP DI'
            );

            $dependencies['dependencyContainer'] = $availableDependencies['dependencyContainer'][$dependencyContainer];
            if ('Other' === $dependencyContainer) {
                $dependencies['dependencyContainer']['package'] = $this->io->ask(
                    'Enter Dependency Container package (<vendor>/<package>)'
                );
                $dependencies['dependencyContainer']['version'] = $this->io->ask(
                    'Enter Dependency Container version',
                    '*'
                );
            }
        }

        if ($this->io->confirm('Do you want to configure PSR-3 Logging?')) {
            $logger = $this->io->choice(
                'Select PSR-3 Logger',
                array_keys($availableDependencies['logger']),
                'Monolog'
            );

            $dependencies['logger'] = $availableDependencies['logger'][$logger];
        }

        return $dependencies;
    }
}
