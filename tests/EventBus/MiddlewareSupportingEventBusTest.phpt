<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\EventBus\EventBus;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use Damejidlo\MessageBus\MiddlewareSupportingMessageBus;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class MiddlewareSupportingEventBusTest extends DjTestCase
{

	public function testHandleWithCorrectOrder() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MiddlewareSupportingMessageBus($middlewareCallbackChainCreator);
		$eventBus = new EventBus($messageBus);

		$event = $this->mockEvent();

		$middleware = $this->mockMiddleware();
		$messageBus->appendMiddleware($middleware);

		$callbackChainCalled = FALSE;

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->withArgs(function (array $actualMiddleware, \Closure $endChainWithCallback) use ($middleware) : bool {
				Assert::same([$middleware], $actualMiddleware);
				return TRUE;
			})->andReturn(function (IDomainEvent $actualEvent) use ($event, &$callbackChainCalled) : void {
				$callbackChainCalled = TRUE;
				Assert::same($event, $actualEvent);
			});

		Assert::noError(function () use ($eventBus, $event) : void {
			$eventBus->handle($event);
		});

		Assert::true($callbackChainCalled);
	}



	public function testHandleFails() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MiddlewareSupportingMessageBus($middlewareCallbackChainCreator);
		$eventBus = new EventBus($messageBus);

		$exception = new \Exception();

		$event = $this->mockEvent();

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->andReturn(function (IDomainEvent $actualEvent) use ($exception) : void {
				throw $exception;
			});

		$actualException = Assert::exception(function () use ($eventBus, $event) : void {
			$eventBus->handle($event);
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
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent() : IDomainEvent
	{
		$mock = Mockery::mock(IDomainEvent::class);

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



(new MiddlewareSupportingEventBusTest())->run();
