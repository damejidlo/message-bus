<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Events;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Events\IEvent;
use Damejidlo\Events\IEventBus;
use Damejidlo\Events\SynchronousEventDispatcher;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class SynchronousEventDispatcherTest extends DjTestCase
{

	public function testThatDispatchDelegates() : void
	{
		$eventBus = $this->mockEventBus();
		$dispatcher = new SynchronousEventDispatcher($eventBus);

		$event = new class() implements IEvent
		{

		};

		// expectations
		$eventBus->shouldReceive('handle')->once()->with($event);

		Assert::noError(function () use ($dispatcher, $event) : void {
			$dispatcher->dispatch($event);
		});
	}



	/**
	 * @return IEventBus|MockInterface
	 */
	private function mockEventBus() : IEventBus
	{
		$mock = Mockery::mock(IEventBus::class);

		return $mock;
	}

}



(new SynchronousEventDispatcherTest())->run();
