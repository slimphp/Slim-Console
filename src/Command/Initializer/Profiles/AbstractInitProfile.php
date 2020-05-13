<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Console\Command\Initializer\Profiles;

use Slim\Console\Config\Config;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_merge;
use function file_put_contents;
use function getcwd;
use function is_array;
use function is_dir;
use function json_encode;
use function mkdir;
use function scandir;

/**
 * Class AbstractInitProfile.
 *
 * @package Slim\Console\Command\Initializer\Profiles
 * @author Temuri Takalandze <me@abgeo.dev>
 */
abstract class AbstractInitProfile implements InitProfileInterface
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var Config|null
     */
    protected $config;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * {@inheritDoc}
     */
    public function __construct(InputInterface $input, OutputInterface $output, ?Config $config = null)
    {
        $this->input = $input;
        $this->output = $output;
        $this->config = $config;
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function run(string $projectDirectory, bool $useDefaultSetup = false): int
    {
        $projectData = null;
        $directoryFullPath = getcwd() . DIRECTORY_SEPARATOR . $projectDirectory;
        $directoryContent = scandir($directoryFullPath);

        if (is_dir($directoryFullPath) && is_array($directoryContent) && count($directoryContent) > 2) {
            $this->io->error("Project directory `{$projectDirectory}` is not empty!");

            return -1;
        }

        if (!is_dir($directoryFullPath)) {
            mkdir($directoryFullPath);
        }

        $projectData = [
            'name' => $this->io->ask('Project name (<vendor>/<name>)', get_current_user() . '/' . $projectDirectory),
            'description' => $this->io->ask('Description', ''),
            'license' => $this->io->ask('License', ''),
        ];

        return $this->createComposerJson($directoryFullPath, $projectData);
    }

    /**
     * Create composer.json file.
     *
     * @param string $directory
     * @param array<string, array> $data
     *
     * @return int The Exit Code.
     */
    public function createComposerJson(string $directory, array $data): int
    {
        $data = array_merge(
            [
                'name' => '',
                'description' => '',
                'license' => '',
                'require' => new stdClass(),
                'require-dev' => new stdClass(),
                'autoload' => [
                    'psr-4' => [
                        'App\\' => $this->config ? $this->config->getSourceDir() : 'src',
                    ],
                ],
                'scripts' => [
                    'start' => 'php -S localhost:8080 -t ' . ($this->config ? $this->config->getIndexDir() : 'public'),
                ],
            ],
            $data
        );

        if (
            !file_put_contents(
                $directory . DIRECTORY_SEPARATOR . 'composer.json',
                json_encode($data, JSON_PRETTY_PRINT)
            )
        ) {
            return -1;
        }

        return 0;
    }
}
