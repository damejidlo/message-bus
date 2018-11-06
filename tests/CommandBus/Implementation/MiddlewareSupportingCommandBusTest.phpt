<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\CommandBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\Implementation\MiddlewareSupportingCommandBus;
use Damejidlo\CommandBus\Implementation\NewEntityId;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class MiddlewareSupportingCommandBusTest extends DjTestCase
{

	public function testHandleWithCorrectOrder() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$commandBus = new MiddlewareSupportingCommandBus($middlewareCallbackChainCreator);

		$command = $this->mockCommand();

		$middleware = $this->mockMiddleware();
		$commandBus->appendMiddleware($middleware);

		$callbackChainCalled = FALSE;
		$newEntityId = new NewEntityId('');

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->withArgs(function (array $actualMiddleware, \Closure $endChainWithCallback) use ($middleware) : bool {
				Assert::same([$middleware], $actualMiddleware);
				return TRUE;
			})->andReturn(function (ICommand $actualCommand) use ($command, &$callbackChainCalled, $newEntityId) : ?NewEntityId {
				$callbackChainCalled = TRUE;
				Assert::same($command, $actualCommand);

				return $newEntityId;
			});

		$result = $commandBus->handle($command);
		Assert::same($newEntityId, $result);

		Assert::true($callbackChainCalled);
	}



	public function testHandleFails() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$commandBus = new MiddlewareSupportingCommandBus($middlewareCallbackChainCreator);

		$exception = new \Exception();

		$command = $this->mockCommand();

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->andReturn(function (ICommand $actualCommand) use ($exception) : void {
				throw $exception;
			});

		$actualException = Assert::exception(function () use ($commandBus, $command) : void {
			$commandBus->handle($command);
		}, \Exception::class);
		Assert::same($exception, $actualException);
	}



	/**
	 * @return MiddlewareCallbackChainCreator|MockInterface
	 */
	private function mockMiddlewareCallbackChainCreator() : MiddlewareCallbackChainCreator
	{
		$mock = Mockery::mock(MiddlewareCallbackChainCreator::class);

		return $mock;
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
	 * @return IMessageBusMiddleware|MockInterface
	 */
	private function mockMiddleware() : IMessageBusMiddleware
	{
		$mock = Mockery::mock(IMessageBusMiddleware::class);

		return $mock;
	}

}



(new MiddlewareSupportingCommandBusTest())->run();
