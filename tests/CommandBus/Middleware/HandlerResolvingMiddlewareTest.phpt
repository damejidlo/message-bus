<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\CommandBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\CommandHandlerNotFoundException;
use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\ICommandHandler;
use Damejidlo\CommandBus\ICommandHandlerProvider;
use Damejidlo\CommandBus\ICommandHandlerResolver;
use Damejidlo\CommandBus\Implementation\NewEntityId;
use Damejidlo\CommandBus\Middleware\HandlerResolvingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class HandlerResolvingMiddlewareTest extends DjTestCase
{

	private const HANDLER_RETURN_VALUE = '42';



	public function testHandleSucceeds() : void
	{
		$command = $this->mockCommand();
		$handler = $this->mockCommandHandler();

		$newEntityId = new NewEntityId(self::HANDLER_RETURN_VALUE);

		$handlerResolver = $this->mockCommandHandlerResolver();
		$handlerResolver->shouldReceive('resolve')->andReturn('some-type');

		$handlerProvider = $this->mockCommandHandlerProvider();
		$handlerProvider->shouldReceive('getByType')->andReturn($handler);

		$middleware = new HandlerResolvingMiddleware(
			$handlerResolver,
			$handlerProvider
		);

		// expectations

		$handler->shouldReceive('handle')->once()->with($command)->andReturn($newEntityId);

		/** @var NewEntityId $result */
		$result = $middleware->handle(
			$command,
			MiddlewareCallback::empty()
		);

		Assert::same(self::HANDLER_RETURN_VALUE, $result->getValue());
	}



	public function testHandleFails() : void
	{
		$command = $this->mockCommand();
		$handler = $this->mockCommandHandler();

		$handlerResolver = $this->mockCommandHandlerResolver();
		$handlerResolver->shouldReceive('resolve')->andThrow(CommandHandlerNotFoundException::class);

		$handlerProvider = $this->mockCommandHandlerProvider();

		$middleware = new HandlerResolvingMiddleware(
			$handlerResolver,
			$handlerProvider
		);

		// expectations
		$handler->shouldReceive('handle')->never();

		Assert::exception(
			function () use ($middleware, $command) : void {
				$middleware->handle(
					$command,
					MiddlewareCallback::empty()
				);
			},
			CommandHandlerNotFoundException::class
		);
	}



	/**
	 * @return ICommand|MockInterface
	 */
	private function mockCommand() : ICommand
	{
		$mock = Mockery::mock(ICommand::class);

		return $mock;
	}



	/**
	 * @return ICommandHandler|MockInterface
	 */
	private function mockCommandHandler() : ICommandHandler
	{
		$mock = Mockery::mock(ICommandHandler::class);

		return $mock;
	}



	/**
	 * @return ICommandHandlerResolver|MockInterface
	 */
	private function mockCommandHandlerResolver() : ICommandHandlerResolver
	{
		$mock = Mockery::mock(ICommandHandlerResolver::class);

		return $mock;
	}



	/**
	 * @return ICommandHandlerProvider|MockInterface
	 */
	private function mockCommandHandlerProvider() : ICommandHandlerProvider
	{
		$mock = Mockery::mock(ICommandHandlerProvider::class);

		return $mock;
	}

}

(new HandlerResolvingMiddlewareTest())->run();
