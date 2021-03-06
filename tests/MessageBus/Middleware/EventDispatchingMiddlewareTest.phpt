<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\IEventDispatcher;
use Damejidlo\MessageBus\Events\InMemoryEventQueue;
use Damejidlo\MessageBus\Middleware\EventDispatchingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class EventDispatchingMiddlewareTest extends DjTestCase
{

	private const CALLBACK_RETURN_VALUE = 1;



	public function testEventsGetDispatchedAfterSuccessfulCommandHandling() : void
	{
		$eventQueue = $this->mockEventQueue();
		$eventDispatcher = $this->mockEventDispatcher();

		$middleware = new EventDispatchingMiddleware(
			$eventQueue,
			$eventDispatcher
		);

		$command = $this->mockCommand();

		$nextMiddlewareCallbackCalled = FALSE;

		$event1 = $this->mockEvent();
		$event2 = $this->mockEvent();

		$events = [
			$event1,
			$event2,
		];

		$eventQueue->shouldReceive('releaseEvents')->andReturn($events);

		// expectations
		foreach ($events as $event) {
			$eventDispatcher->shouldReceive('dispatch')->once()->with($event);
		}

		$result = $middleware->handle(
			$command,
			MiddlewareContext::empty(),
			MiddlewareCallback::fromClosure(
				function (ICommand $command) use (&$nextMiddlewareCallbackCalled) {
					$nextMiddlewareCallbackCalled = TRUE;

					return self::CALLBACK_RETURN_VALUE;
				}
			)
		);

		Assert::same(self::CALLBACK_RETURN_VALUE, $result);

		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testNoEventsGetDispatchedWhenCommandHandlingFails() : void
	{
		$eventQueue = $this->mockEventQueue();
		$eventDispatcher = $this->mockEventDispatcher();

		$middleware = new EventDispatchingMiddleware(
			$eventQueue,
			$eventDispatcher
		);

		$command = $this->mockCommand();

		$nextMiddlewareCallbackCalled = FALSE;

		$event1 = $this->mockEvent();
		$event2 = $this->mockEvent();

		$events = [
			$event1,
			$event2,
		];

		// expectations
		$eventDispatcher->shouldReceive('dispatch')->never();
		$eventQueue->shouldReceive('releaseEvents')->once()->andReturn($events);

		Assert::exception(
			function () use ($middleware, $command, &$nextMiddlewareCallbackCalled) : void {
				$middleware->handle(
					$command,
					MiddlewareContext::empty(),
					MiddlewareCallback::fromClosure(
						function (ICommand $command) use (&$nextMiddlewareCallbackCalled) : void {
							$nextMiddlewareCallbackCalled = TRUE;
							throw new \Exception();
						}
					)
				);
			},
			\Exception::class
		);

		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleFailsWhenEventDispatchFails() : void
	{
		$eventQueue = $this->mockEventQueue();
		$eventDispatcher = $this->mockEventDispatcher();

		$middleware = new EventDispatchingMiddleware(
			$eventQueue,
			$eventDispatcher
		);

		$command = $this->mockCommand();

		$nextMiddlewareCallbackCalled = FALSE;

		$event1 = $this->mockEvent();
		$event2 = $this->mockEvent();

		$events = [
			$event1,
			$event2,
		];

		// expectations
		$eventQueue->shouldReceive('releaseEvents')->once()->andReturn($events);
		$eventDispatcher->shouldReceive('dispatch')->once()->andThrow(\Exception::class);
		$eventQueue->shouldReceive('releaseEvents')->once()->andReturn([]);

		Assert::exception(
			function () use ($middleware, $command, &$nextMiddlewareCallbackCalled) : void {
				$middleware->handle(
					$command,
					MiddlewareContext::empty(),
					MiddlewareCallback::fromClosure(
						function (ICommand $command) use (&$nextMiddlewareCallbackCalled) : void {
							$nextMiddlewareCallbackCalled = TRUE;
						}
					)
				);
			},
			\Exception::class
		);

		Assert::true($nextMiddlewareCallbackCalled);
	}



	/**
	 * @return InMemoryEventQueue|MockInterface
	 */
	private function mockEventQueue() : InMemoryEventQueue
	{
		$mock = Mockery::mock(InMemoryEventQueue::class);

		return $mock;
	}



	/**
	 * @return IEventDispatcher|MockInterface
	 */
	private function mockEventDispatcher() : IEventDispatcher
	{
		$mock = Mockery::mock(IEventDispatcher::class);

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
	 * @return IEvent|MockInterface
	 */
	private function mockEvent() : IEvent
	{
		$mock = Mockery::mock(IEvent::class);

		return $mock;
	}

}



(new EventDispatchingMiddlewareTest())->run();
