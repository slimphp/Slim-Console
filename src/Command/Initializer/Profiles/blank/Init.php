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
     * @var bool
     */
    private $useDefaultSetup;

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
        $this->useDefaultSetup = $useDefaultSetup;
        $exitCode = null;

        if (0 !== ($exitCode = parent::run($projectDirectory, $useDefaultSetup))) {
            return $exitCode;
        }

        if (0 !== ($exitCode = $this->createStructure($projectDirectory))) {
            return $exitCode;
        }

        if (0 === ($exitCode = $this->setupDependencies($projectDirectory))) {
            $this->io->success('New Slim project successfully created. Please run `composer install`.');
        }

        return $exitCode;
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

        if ($this->useDefaultSetup ? true : $this->io->confirm('Do you want to create docker-compose.yml?', true)) {
            copy(
                $this->templatesDirectory . DIRECTORY_SEPARATOR . 'docker-compose.yml.template',
                $directoryFullPath . DIRECTORY_SEPARATOR . 'docker-compose.yml'
            );
        }

        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . '.gitignore.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . '.gitignore'
        );
        copy(
            $this->templatesDirectory . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . '.htaccess.template',
            $directoryFullPath . DIRECTORY_SEPARATOR . $directoriesToCreate['index'] . DIRECTORY_SEPARATOR . '.htaccess'
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
        $directoryFullPath = getcwd() . DIRECTORY_SEPARATOR . $projectDirectory;
        $composerJsonContent = $this->readComposerJson($directoryFullPath);
        $bootstrapDirectory = $this->config ? $this->config->getBootstrapDir() : 'app';
        $indexDirectory = $this->config ? $this->config->getIndexDir() : 'public';
        $dependencies = $this->askDependencies();
        $templates = [
            'sourcePrefix' => $this->templatesDirectory . DIRECTORY_SEPARATOR,
            'destinationPrefix' => $directoryFullPath . DIRECTORY_SEPARATOR,
            'templates' => [
                'routes' => [
                    'sourceFile' => 'app' . DIRECTORY_SEPARATOR . 'return_function.php.template',
                    'destinationFile' => $bootstrapDirectory . DIRECTORY_SEPARATOR . 'routes.php',
                    'template' => '',
                    'replaces' => [],
                ],
                'settings' => [
                    'sourceFile' => 'app' . DIRECTORY_SEPARATOR . 'return_function.php.template',
                    'destinationFile' => $bootstrapDirectory . DIRECTORY_SEPARATOR . 'settings.php',
                    'template' => '',
                    'replaces' => [],
                ],
                'dependencies' => [
                    'sourceFile' => 'app' . DIRECTORY_SEPARATOR . 'return_function.php.template',
                    'destinationFile' => $bootstrapDirectory . DIRECTORY_SEPARATOR . 'dependencies.php',
                    'template' => '',
                    'replaces' => [],
                ],
                'middleware' => [
                    'sourceFile' => 'app' . DIRECTORY_SEPARATOR . 'return_function.php.template',
                    'destinationFile' => $bootstrapDirectory . DIRECTORY_SEPARATOR . 'middleware.php',
                    'template' => '',
                    'replaces' => [],
                ],
                'index' => [
                    'sourceFile' => 'public' . DIRECTORY_SEPARATOR . 'index.php.template',
                    'destinationFile' => $indexDirectory . DIRECTORY_SEPARATOR . 'index.php',
                    'template' => '',
                    'replaces' => [],
                ],
            ],
        ];

        foreach ($dependencies as $dependency) {
            foreach ($dependency['packages'] as $package => $version) {
                $composerJsonContent['require'][$package] = $version;
            }
        }

        // Read templates.
        foreach ($templates['templates'] as $k => $template) {
            $templates['templates'][$k]['template'] = file_get_contents(
                $templates['sourcePrefix'] . $template['sourceFile']
            );
        }

        $templates['templates']['routes']['replaces']['{argument}'] = 'App $app';
        $templates['templates']['routes']['replaces']['{body}'] = <<<'BODY'

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

BODY;

        switch ($dependencies['psr7']['id']) {
            case 'slim_psr_7':
                $templates['templates']['routes']['replaces']['{imports}'] =
                    "\nuse Psr\Http\Message\ResponseInterface as Response;\n" .
                    "use Psr\Http\Message\ServerRequestInterface as Request;\nuse Slim\App;\n";
                break;
            case 'laminas':
                $templates['templates']['routes']['replaces']['{imports}'] =
                    "\nuse Laminas\Diactoros\ServerRequest as Request;\n" .
                    "use Laminas\Diactoros\Response;\nuse Slim\App;\n";
                break;
            case 'guzzle':
                $templates['templates']['routes']['replaces']['{imports}'] = "\nuse GuzzleHttp\Psr7\Request;\n" .
                    "use GuzzleHttp\Psr7\Response;\nuse Slim\App;\n";
                break;
            case 'nyholm':
                $templates['templates']['routes']['replaces']['{imports}'] = "\nuse Nyholm\Psr7\Response;\n" .
                    "use Nyholm\Psr7\ServerRequest as Request;\nuse Slim\App;\n";
                break;
        }

        switch ($dependencies['dependencyContainer']['id']) {
            case 'php_di':
                $templates['templates']['settings']['replaces']['{imports}'] = "\nuse DI\ContainerBuilder;\n" .
                    "use Monolog\Logger;\n";
                $templates['templates']['settings']['replaces']['{argument}'] = 'ContainerBuilder $containerBuilder';
                $templates['templates']['settings']['replaces']['{body}'] = <<<'BODY'

    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => '{appName}',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
        ],
    ]);

BODY;

                $templates['templates']['dependencies']['replaces']['{imports}'] = "\nuse DI\ContainerBuilder;\n" .
                    "use Monolog\Handler\StreamHandler;\nuse Monolog\Logger;\nuse Monolog\Processor\UidProcessor;\n" .
                    "use Psr\Container\ContainerInterface;\nuse Psr\Log\LoggerInterface;\n";
                $templates['templates']['dependencies']['replaces']['{argument}']
                    = 'ContainerBuilder $containerBuilder';
                $templates['templates']['dependencies']['replaces']['{body}'] = <<<'BODY'

    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);
                        
            $processor = new UidProcessor();
            $logger->pushProcessor($processor);
                        
            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);
                        
            return $logger;
        },
    ]);

BODY;

                $templates['templates']['index']['replaces']['{containerVariable}'] = '$containerBuilder';
                $templates['templates']['index']['replaces']['{imports}'] = "use DI\ContainerBuilder;\n" .
                    "use Slim\Factory\AppFactory;";
                $templates['templates']['index']['replaces']['{defineContainer}'] = <<<'BODY'
// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}
BODY;
                $templates['templates']['index']['replaces']['{setContainer}'] = <<<'BODY'
// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
BODY;
                break;
            case 'pimple':
                $templates['templates']['settings']['replaces']['{imports}'] = "\nuse Monolog\Logger;\n" .
                    "use Pimple\Container;\n";
                $templates['templates']['settings']['replaces']['{argument}'] = 'Container $container';
                $templates['templates']['settings']['replaces']['{body}'] = <<<'BODY'

    // Global Settings Object
    $container['settings'] = [
        'displayErrorDetails' => true, // Should be set to false in production
        'logger' => [
            'name' => '{appName}',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => Logger::DEBUG,
        ],
    ];

BODY;

                $templates['templates']['dependencies']['replaces']['{imports}'] =
                    "\nuse Monolog\Handler\StreamHandler;\nuse Monolog\Logger;\nuse Monolog\Processor\UidProcessor;\n" .
                    "use Pimple\Container;\nuse Psr\Log\LoggerInterface;\n";
                $templates['templates']['dependencies']['replaces']['{argument}'] = 'Container $container';
                $templates['templates']['dependencies']['replaces']['{body}'] = <<<'BODY'

    $container[LoggerInterface::class] = function ($c) {
        $settings = $c['settings'];
        
        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);
                        
        $processor = new UidProcessor();
        $logger->pushProcessor($processor);
                        
        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);
                        
        return $logger;
    };

BODY;

                $templates['templates']['index']['replaces']['{containerVariable}'] = '$container';
                $templates['templates']['index']['replaces']['{imports}'] = "use Pimple\Container;\n" .
                    "use Slim\Factory\AppFactory;";
                $templates['templates']['index']['replaces']['{defineContainer}'] = <<<'BODY'
// Instantiate Pimple Container
$container = new Container();
BODY;
                $templates['templates']['index']['replaces']['{setContainer}'] = <<<'BODY'
// Instantiate the app
AppFactory::setContainer(new \Pimple\Psr11\Container($container));
BODY;
                break;
            case 'other':
                $templates['templates']['settings']['replaces']['{imports}'] = null;
                $templates['templates']['settings']['replaces']['{argument}'] = '$container';
                $templates['templates']['settings']['replaces']['{body}'] = "\n";

                $templates['templates']['dependencies']['replaces']['{imports}'] = null;
                $templates['templates']['dependencies']['replaces']['{argument}'] = '$container';
                $templates['templates']['dependencies']['replaces']['{body}'] = "\n";

                $templates['templates']['index']['replaces']['{containerVariable}'] = '$container';
                $templates['templates']['index']['replaces']['{imports}'] = "use Slim\Factory\AppFactory;";
                $templates['templates']['index']['replaces']['{defineContainer}'] = <<<'BODY'
// TODO: Instantiate you'r Dependency Container
$container = null;
BODY;
                $templates['templates']['index']['replaces']['{setContainer}'] = <<<'BODY'
// Instantiate the app
// TODO: Uncomment the line below if you created an instance of Dependency Container
//AppFactory::setContainer($container);
BODY;
                break;
        }

        $templates['templates']['settings']['replaces']['{appName}'] = $projectDirectory;

        $templates['templates']['middleware']['replaces']['{imports}'] = "\nuse Slim\App;\n";
        $templates['templates']['middleware']['replaces']['{argument}'] = 'App $app';
        $templates['templates']['middleware']['replaces']['{body}'] = "\n";

        // Replace tokens in templates and write to destination files.
        foreach ($templates['templates'] as $k => $template) {
            foreach ($template['replaces'] as $token => $replace) {
                $template['template'] = str_replace($token, $replace, (string)$template['template']);
            }

            file_put_contents(
                $templates['destinationPrefix'] . $template['destinationFile'],
                $template['template']
            );
        }

        return $this->writeToComposerJson($directoryFullPath, $composerJsonContent);
    }

    /**
     * Collect dependencies from user input.
     *
     * @return array<array>
     */
    protected function askDependencies(): array
    {
        $psr7 = null;
        $dependencyContainer = null;
        $logger = null;
        $availableDependencies = [
            'psr7' => [
                'Slim PSR-7' => [
                    'id'       => 'slim_psr_7',
                    'packages' => [
                        'slim/psr7' => Versions::SLIM_PSR_7,
                    ],
                ],
                'Laminas' => [
                    'id'       => 'laminas',
                    'packages' => [
                        'laminas/laminas-diactoros' => Versions::LAMINAS,
                    ],
                ],
                'Guzzle' => [
                    'id'       => 'guzzle',
                    'packages' => [
                        'guzzlehttp/psr7' => Versions::GIZZLE_PSR_7,
                        'http-interop/http-factory-guzzle' => Versions::HTTP_FACTORY_GUZZLE,
                    ],
                ],
                'Nyholm' => [
                    'id'       => 'nyholm',
                    'packages' => [
                        'nyholm/psr7'        => Versions::NYHOLM_PSR_7,
                        'nyholm/psr7-server' => Versions::NYHOLM_PSR_7_SERVER,
                    ],
                ],
            ],
            'dependencyContainer' => [
                'PHP DI' => [
                    'id'       => 'php_di',
                    'packages' => [
                        'php-di/php-di' => Versions::PHP_DI,
                    ],
                ],
                'Pimple' => [
                    'id'       => 'pimple',
                    'packages' => [
                        'pimple/pimple' => Versions::PIMPIE,
                    ],
                ],
                'Other' => [
                    'id'       => 'other',
                    'packages' => [],
                ],
            ],
            'logger' => [
                'Monolog' => [
                    'id'       => 'monolog',
                    'packages' => [
                        'monolog/monolog' => Versions::MONOLOG,
                    ],
                ],
            ],
        ];
        $dependencies = [
            'psr7' => $availableDependencies['psr7']['Slim PSR-7'],
            'dependencyContainer' => $availableDependencies['dependencyContainer']['PHP DI'],
            'logger' => $availableDependencies['logger']['Monolog'],
        ];

        if (!$this->useDefaultSetup) {
            if ($this->io->confirm('Do you want to configure the PSR-7 HTTP message interface?')) {
                $psr7 = $this->io->choice(
                    'Select PSR-7 implementation',
                    array_keys($availableDependencies['psr7']),
                    'Slim PSR-7'
                );

                $dependencies['psr7'] = $availableDependencies['psr7'][$psr7];
            }

            if ($this->io->confirm('Do you want to configure Dependency Container?')) {
                $dependencyContainer = $this->io->choice(
                    'Select Dependency Container',
                    array_keys($availableDependencies['dependencyContainer']),
                    'PHP DI'
                );

                $dependencies['dependencyContainer'] =
                    $availableDependencies['dependencyContainer'][$dependencyContainer];
                if ('Other' === $dependencyContainer) {
                    $dependencies['dependencyContainer']['packages'] = [
                        $this->io->ask('Enter Dependency Container package (<vendor>/<package>)')
                        => $this->io->ask('Enter Dependency Container version', '*'),
                    ];
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
        }

        return $dependencies;
    }
}
