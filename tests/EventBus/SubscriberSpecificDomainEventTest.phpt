<?php
declare(strict_types = 1);

namespace DamejidloTests\EventBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\ILoggableBusMessage;
use DamejidloTests\DjTestCase;
use Mockery\MockInterface;
use Tester\Assert;



/**
 * @testCase
 */
class SubscriberSpecificDomainEventTest extends DjTestCase
{

	private const SUBSCRIBER_TYPE = 'Foo';



	public function testGetLoggingContext() : void
	{
		$event = $this->mockEvent();
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);
		Assert::same(
			[
				'subscriberType' => self::SUBSCRIBER_TYPE,
				'eventType' => get_class($event),
			],
			$subscriberSpecificDomainEvent->getLoggingContext()
		);
	}



	public function testGetLoggingContextWithLoggableMessage() : void
	{
		$eventAsArray = ['property' => 'value'];
		$event = $this->mockLoggableEvent($eventAsArray);
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);
		Assert::same(
			[
				'subscriberType' => self::SUBSCRIBER_TYPE,
				'eventType' => get_class($event),
				'property' => 'value',
			],
			$subscriberSpecificDomainEvent->getLoggingContext()
		);
	}



	/**
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent() : IDomainEvent
	{
		$mock = \Mockery::mock(IDomainEvent::class);

		return $mock;
	}



	/**
	 * @param mixed[] $asArray
	 * @return IDomainEvent&ILoggableBusMessage&MockInterface
	 */
	private function mockLoggableEvent(array $asArray) : ILoggableBusMessage
	{
		$implements = [
			IDomainEvent::class,
			ILoggableBusMessage::class,
		];

		/** @var IDomainEvent&ILoggableBusMessage&MockInterface $mock */
		$mock = \Mockery::mock(implode(',', $implements));
		$mock->shouldReceive('getLoggingContext')->andReturn($asArray);

		return $mock;
	}

}



(new SubscriberSpecificDomainEventTest())->run();
