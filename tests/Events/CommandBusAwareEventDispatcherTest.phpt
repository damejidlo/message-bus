<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Events;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\Events\CommandBusAwareEventDispatcher;
use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\IEventDispatcher;
use Damejidlo\MessageBus\Events\InMemoryEventQueue;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class CommandBusAwareEventDispatcherTest extends DjTestCase
{

	public function testEnqueueWhenUsingCommandBus() : void
	{
		$isCurrentlyHandlingAwareMiddleware = $this->mockIsCurrentlyHandlingAwareMiddleware();
		$eventQueue = $this->mockEventQueue();
		$delegate = $this->mockEventDispatcher();

		$dispatcher = new CommandBusAwareEventDispatcher(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$delegate
		);

		$event = $this->mockEvent();


		$isCurrentlyHandlingAwareMiddleware->shouldReceive('isHandling')->andReturn(TRUE);

		// expectations
		$eventQueue->shouldReceive('enqueue')->once()->with($event);

		Assert::noError(function () use ($dispatcher, $event) : void {
			$dispatcher->dispatch($event);
		});
	}



	public function testEnqueueWhenNotUsingCommandBus() : void
	{
		$isCurrentlyHandlingAwareMiddleware = $this->mockIsCurrentlyHandlingAwareMiddleware();
		$eventQueue = $this->mockEventQueue();
		$delegate = $this->mockEventDispatcher();

		$queue = new CommandBusAwareEventDispatcher(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$delegate
		);

		$event = $this->mockEvent();

		$isCurrentlyHandlingAwareMiddleware->shouldReceive('isHandling')->andReturn(FALSE);

		// expectations
		$delegate->shouldReceive('dispatch')->once()->with($event);

		Assert::noError(function () use ($queue, $event) : void {
			$queue->dispatch($event);
		});
	}



	/**
	 * @return IsCurrentlyHandlingAwareMiddleware|MockInterface
	 */
	private function mockIsCurrentlyHandlingAwareMiddleware() : IsCurrentlyHandlingAwareMiddleware
	{
		$mock = Mockery::mock(IsCurrentlyHandlingAwareMiddleware::class);

		return $mock;
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
	 * @return IEvent|MockInterface
	 */
	private function mockEvent() : IEvent
	{
		$mock = Mockery::mock(IEvent::class);

		return $mock;
	}

}



(new CommandBusAwareEventDispatcherTest())->run();
