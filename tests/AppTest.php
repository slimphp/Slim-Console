<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Console/blob/0.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Console;

use Prophecy\Argument;
use Slim\Console\Config\Config;
use Slim\Console\App;
use Slim\Tests\Console\Mocks\MockCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppTest extends TestCase
{
    public function testDoRun(): void
    {
        $config = Config::fromDefaults();

        $mockCommand = new MockCommand();

        $app = new App($config);
        $app->add($mockCommand);

        $inputInterfaceProphecy = $this->prophesize(InputInterface::class);

        $inputInterfaceProphecy
            ->hasParameterOption(['--help', '-h'], true)
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->hasParameterOption(['--help', '-h'])
            ->willReturn(true)
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->isInteractive()
            ->willReturn(true)
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->hasArgument('command')
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->validate()
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->hasParameterOption(['--version', '-V'], true)
            ->willReturn(false)
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->getFirstArgument()
            ->willReturn(MockCommand::getDefaultName())
            ->shouldBeCalledOnce();

        $inputInterfaceProphecy
            ->bind(Argument::any())
            ->shouldBeCalledTimes(2);

        $outputInterfaceProphecy = $this->prophesize(OutputInterface::class);

        $outputInterfaceProphecy
            ->writeln($app->getLongVersion())
            ->shouldBeCalledOnce();

        $outputInterfaceProphecy
            ->writeln('')
            ->shouldBeCalledOnce();

        $outputInterfaceProphecy
            ->writeln($mockCommand->getMockOutput())
            ->shouldBeCalledOnce();

        $app->doRun($inputInterfaceProphecy->reveal(), $outputInterfaceProphecy->reveal());
    }

    public function testGetConfig(): void
    {
        $config = Config::fromDefaults();

        $app = new App($config);

        $this->assertSame($config, $app->getConfig());
    }
}
