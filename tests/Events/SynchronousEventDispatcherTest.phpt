<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Events;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\SynchronousEventDispatcher;
use Damejidlo\MessageBus\IMessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class SynchronousEventDispatcherTest extends DjTestCase
{

	public function testThatDispatchDelegates() : void
	{
		$messageBus = $this->mockMessageBus();
		$dispatcher = new SynchronousEventDispatcher($messageBus);

		$event = new class() implements IEvent
		{

		};

		// expectations
		$messageBus->shouldReceive('handle')->once()->withArgs(
			function (IEvent $actualEvent, MiddlewareContext $context) use ($event) : bool {
				Assert::equal($event, $actualEvent);
				Assert::equal(MiddlewareContext::empty(), $context);
				return TRUE;
			}
		);

		Assert::noError(function () use ($dispatcher, $event) : void {
			$dispatcher->dispatch($event);
		});
	}



	/**
	 * @return IMessageBus|MockInterface
	 */
	private function mockMessageBus() : IMessageBus
	{
		$mock = Mockery::mock(IMessageBus::class);

		return $mock;
	}

}



(new SynchronousEventDispatcherTest())->run();
