<?php

declare(strict_types=1);

namespace Slim\Tests\Console\Config;

use PHPUnit\Framework\TestCase;
use Slim\Console\Config\Config;

class ConfigTest extends TestCase
{
    /** @var Config */
    private $config;

    public function setUp(): void
    {
        //Suppose that is APP dir
        $configDir = __DIR__;
        $this->config = new Config([], $configDir);

        $this->config->set('rootDir', $configDir);
    }
    public function testShouldBuildWithDefaultParams(): void
    {
        $this->assertArrayHasKey('bootstrapDir', $this->config);
        $this->assertArrayHasKey('commandsDir', $this->config);
        $this->assertArrayHasKey('indexDir', $this->config);
        $this->assertArrayHasKey('indexFile', $this->config);
        $this->assertArrayHasKey('sourceDir', $this->config);
    }

    public function testShouldGetValue(): void
    {
        $this->assertIsString($this->config->get('rootDir'));
        $this->assertIsString($this->config['rootDir']);
        $this->assertFalse(empty($this->config->get('rootDir')));
        $this->assertFalse(empty($this->config['rootDir']));
    }

    public function testShouldHasKey(): void
    {
        $this->config->set('foo', 'bar');
        $this->assertTrue($this->config->has('foo'));
        $this->config->delete('foo');

        $this->config['f'] = 'b';
        $this->assertIsString($this->config['f']);

        $this->assertFalse($this->config->has('doesnotexist'));
    }

    public function testShouldDeleteParam(): void
    {
        $this->config->set('foo', 'bar');
        $this->config->set('f', 'b');
        $this->assertArrayHasKey('foo', $this->config);
        $this->assertArrayHasKey('f', $this->config);

        $this->config->delete('foo');
        unset($this->config['f']);
        $this->assertArrayNotHasKey('foo', $this->config);
        $this->assertArrayNotHasKey('f', $this->config);
    }

    public function testShouldGivePrecedenceToEnvVarialbesOfDefaultParams(): void
    {
        $this->assertArrayHasKey('sourceDir', $this->config);

        //it is case sensitive
        putenv('SLIM_CONSOLE_SOURCE_DIR=Source dir taken from env');
        putenv('SLIM_CONSOLE_INDEX_FILE=Index file taken from env');
        $config = new Config();

        $this->assertEquals('Source dir taken from env', $config->get('sourceDir'));
        $this->assertEquals('Index file taken from env', $config->get('indexFile'));

        //Unset env variables
        putenv('SLIM_CONSOLE_SOURCE_DIR');
        putenv('SLIM_CONSOLE_INDEX_FILE');
    }

    public function testShouldAddMoreParametersBeyondDefault(): void
    {
        $this->config->set('param1', 'value1');
        $this->assertArrayHasKey('param1', $this->config);

        $this->config->set('param2', 'value2');
        $this->assertEquals('value2', $this->config->get('param2'));
    }

    public function testShouldtheDefaulsHaveAbsolutePaths(): void
    {
        $dir = '.';
        $config = new Config([], $dir);
        $this->assertEquals([
            'bootstrapDir' => $dir . DIRECTORY_SEPARATOR . 'app',
            'commandsDir'  => $dir . DIRECTORY_SEPARATOR . 'Application/Commands',
            'indexDir'     => $dir . DIRECTORY_SEPARATOR . 'public',
            'indexFile'    => $dir . DIRECTORY_SEPARATOR . 'index.php',
            'sourceDir'    => $dir . DIRECTORY_SEPARATOR . 'src',
            'rootDir'      => $dir,
        ], $config->all());
    }
}
