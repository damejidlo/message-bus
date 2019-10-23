<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Events;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\InMemoryEventQueue;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class InMemoryEventQueueTest extends DjTestCase
{

	public function testEnqueueAndRelease() : void
	{
		$queue = new InMemoryEventQueue();

		$event1 = $this->mockEvent();
		$event2 = $this->mockEvent();

		$queue->enqueue($event1);
		$queue->enqueue($event2);

		Assert::same([
			$event1,
			$event2,
		], $queue->releaseEvents());

		Assert::same([], $queue->releaseEvents());
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



(new InMemoryEventQueueTest())->run();
