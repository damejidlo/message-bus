<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\CommandBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\CommandHandlerNotFoundException;
use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\Implementation\CommandHandlerResolver;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class CommandHandlerResolverTest extends DjTestCase
{

	public function testResolveSucceeds() : void
	{
		$command = $this->mockCommand();
		$commandType = get_class($command);
		$handlerServiceName = 'service-name';

		$resolver = new CommandHandlerResolver();
		$resolver->registerHandler($commandType, $handlerServiceName);

		Assert::same($handlerServiceName, $resolver->resolve($command));
	}



	public function testResolveFailsWhenHandlerNotRegistered() : void
	{
		$command = $this->mockCommand();

		$resolver = new CommandHandlerResolver();

		Assert::exception(function () use ($command, $resolver) : void {
			$resolver->resolve($command);
		}, CommandHandlerNotFoundException::class);
	}



	/**
	 * @return ICommand|MockInterface
	 */
	private function mockCommand() : ICommand
	{
		$mock = Mockery::mock(ICommand::class);

		return $mock;
	}

}

(new CommandHandlerResolverTest())->run();
