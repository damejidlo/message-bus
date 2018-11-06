<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\Implementation\InMemoryEventQueue;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class InMemoryEventQueueTest extends DjTestCase
{

	public function testEnqueueAndRelease() : void
	{
		$queue = new InMemoryEventQueue();

		$event1 = $this->mockIDomainEvent();
		$event2 = $this->mockIDomainEvent();

		$queue->enqueue($event1);
		$queue->enqueue($event2);

		Assert::same([
			$event1,
			$event2,
		], $queue->releaseEvents());

		Assert::same([], $queue->releaseEvents());
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



(new InMemoryEventQueueTest())->run();
