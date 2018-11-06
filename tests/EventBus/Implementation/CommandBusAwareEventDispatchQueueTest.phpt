<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventDispatchQueue;
use Damejidlo\EventBus\Implementation\CommandBusAwareEventDispatchQueue;
use Damejidlo\EventBus\Implementation\InMemoryEventQueue;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class CommandBusAwareEventDispatchQueueTest extends DjTestCase
{

	public function testEnqueueWhenUsingCommandBus() : void
	{
		$isCurrentlyHandlingAwareMiddleware = $this->mockIsCurrentlyHandlingAwareMiddleware();
		$eventQueue = $this->mockEventQueue();
		$eventDispatchQueue = $this->mockEventDispatchQueue();

		$queue = new CommandBusAwareEventDispatchQueue(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$eventDispatchQueue
		);

		$event = $this->mockIDomainEvent();


		$isCurrentlyHandlingAwareMiddleware->shouldReceive('isHandling')->andReturn(TRUE);

		// expectations
		$eventQueue->shouldReceive('enqueue')->once()->with($event);

		Assert::noError(function () use ($queue, $event) : void {
			$queue->enqueue($event);
		});
	}



	public function testEnqueueWhenNotUsingCommandBus() : void
	{
		$isCurrentlyHandlingAwareMiddleware = $this->mockIsCurrentlyHandlingAwareMiddleware();
		$eventQueue = $this->mockEventQueue();
		$eventDispatchQueue = $this->mockEventDispatchQueue();

		$queue = new CommandBusAwareEventDispatchQueue(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$eventDispatchQueue
		);

		$event = $this->mockIDomainEvent();


		$isCurrentlyHandlingAwareMiddleware->shouldReceive('isHandling')->andReturn(FALSE);

		// expectations
		$eventDispatchQueue->shouldReceive('enqueue')->once()->with($event);

		Assert::noError(function () use ($queue, $event) : void {
			$queue->enqueue($event);
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
	 * @return IEventDispatchQueue|MockInterface
	 */
	private function mockEventDispatchQueue() : IEventDispatchQueue
	{
		$mock = Mockery::mock(IEventDispatchQueue::class);

		return $mock;
	}



	/**
	 * @return IDomainEvent|MockInterface
	 */
	private function mockIDomainEvent() : IDomainEvent
	{
		$mock = Mockery::mock(IDomainEvent::class);

		return $mock;
	}

}



(new CommandBusAwareEventDispatchQueueTest())->run();
