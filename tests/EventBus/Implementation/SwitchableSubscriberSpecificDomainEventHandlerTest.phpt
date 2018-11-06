<?php
declare(strict_types = 1);

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\Damejidlo\Switchers\SwitcherIds;
use Damejidlo\EventBus\Implementation\AsynchronousSubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\Implementation\SwitchableSubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\Implementation\SynchronousSubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\Switchers\Switchers;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\TestHappenedEvent;
use Mockery\MockInterface;
use Tester\Assert;



/**
 * @testCase
 */
class SwitchableSubscriberSpecificDomainEventHandlerTest extends DjTestCase
{

	public function testHandleSynchronously() : void
	{
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent(new TestHappenedEvent(), 'SubscriberType');

		$switchers = $this->mockSwitchers(FALSE);
		$synchronousSubscriberSpecificDomainEventHandler = $this->mockSynchronousSubscriberSpecificDomainEventHandler();
		$synchronousSubscriberSpecificDomainEventHandler->shouldReceive('handle')->withArgs([$subscriberSpecificDomainEvent])->once();
		$asynchronousSubscriberSpecificDomainEventHandler = $this->mockAsynchronousSubscriberSpecificDomainEventHandler();

		$handler = new SwitchableSubscriberSpecificDomainEventHandler(
			$switchers,
			$synchronousSubscriberSpecificDomainEventHandler,
			$asynchronousSubscriberSpecificDomainEventHandler
		);
		Assert::noError(
			function () use ($handler, $subscriberSpecificDomainEvent) : void {
				$handler->handle($subscriberSpecificDomainEvent);
			}
		);
	}



	public function testHandleAsynchronously() : void
	{
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent(new TestHappenedEvent(), 'SubscriberType');

		$switchers = $this->mockSwitchers(TRUE);
		$synchronousSubscriberSpecificDomainEventHandler = $this->mockSynchronousSubscriberSpecificDomainEventHandler();
		$asynchronousSubscriberSpecificDomainEventHandler = $this->mockAsynchronousSubscriberSpecificDomainEventHandler();
		$asynchronousSubscriberSpecificDomainEventHandler->shouldReceive('handle')->withArgs([$subscriberSpecificDomainEvent])->once();

		$handler = new SwitchableSubscriberSpecificDomainEventHandler(
			$switchers,
			$synchronousSubscriberSpecificDomainEventHandler,
			$asynchronousSubscriberSpecificDomainEventHandler
		);
		Assert::noError(
			function () use ($handler, $subscriberSpecificDomainEvent) : void {
				$handler->handle($subscriberSpecificDomainEvent);
			}
		);
	}



	/**
	 * @param bool $isTurnedOn
	 * @return Switchers|MockInterface
	 */
	private function mockSwitchers(bool $isTurnedOn) : Switchers
	{
		$mock = \Mockery::mock(Switchers::class);
		$mock->shouldReceive('isSwitcherTurnedOn')->withArgs([SwitcherIds::ASYNCHRONOUS_EVENT_BUS])->andReturn($isTurnedOn);

		return $mock;
	}



	/**
	 * @return SynchronousSubscriberSpecificDomainEventHandler|MockInterface
	 */
	private function mockSynchronousSubscriberSpecificDomainEventHandler() : SynchronousSubscriberSpecificDomainEventHandler
	{
		$mock = \Mockery::mock(SynchronousSubscriberSpecificDomainEventHandler::class);

		return $mock;
	}



	/**
	 * @return AsynchronousSubscriberSpecificDomainEventHandler|MockInterface
	 */
	private function mockAsynchronousSubscriberSpecificDomainEventHandler() : AsynchronousSubscriberSpecificDomainEventHandler
	{
		$mock = \Mockery::mock(AsynchronousSubscriberSpecificDomainEventHandler::class);

		return $mock;
	}

}



(new SwitchableSubscriberSpecificDomainEventHandlerTest())->run();
