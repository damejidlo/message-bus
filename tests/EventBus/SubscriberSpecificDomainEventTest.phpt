<?php
declare(strict_types = 1);

namespace DamejidloTests\EventBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use DamejidloTests\DjTestCase;
use Mockery\MockInterface;
use Tester\Assert;



/**
 * @testCase
 */
class SubscriberSpecificDomainEventTest extends DjTestCase
{

	private const SUBSCRIBER_TYPE = 'Foo';



	public function testToArray() : void
	{
		$eventAsArray = ['property' => 'value'];
		$event = $this->mockEvent($eventAsArray);
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);
		Assert::same(
			[
				'subscriberType' => self::SUBSCRIBER_TYPE,
				'eventType' => get_class($event),
				'property' => 'value',
			],
			$subscriberSpecificDomainEvent->toArray()
		);
	}



	/**
	 * @param mixed[] $asArray
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent(array $asArray) : IDomainEvent
	{
		$mock = \Mockery::mock(IDomainEvent::class);
		$mock->shouldReceive('toArray')->andReturn($asArray);

		return $mock;
	}

}



(new SubscriberSpecificDomainEventTest())->run();
