<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\Implementation\EventSubscribersResolver;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class EventSubscribersResolverTest extends DjTestCase
{

	public function testResolveSucceedsWithNoSubscribers() : void
	{
		$event = $this->mockEvent();

		$resolver = new EventSubscribersResolver();

		Assert::same([], $resolver->resolve($event));
	}



	public function testResolveSucceedsWithOneSubscriber() : void
	{
		$subscriberType = 'FooSubscriber';

		$event = $this->mockEvent();
		$eventType = get_class($event);

		$resolver = new EventSubscribersResolver();
		$resolver->registerSubscriber($eventType, $subscriberType);

		Assert::same([$subscriberType], $resolver->resolve($event));
	}



	public function testResolveSucceedsWithMoreSubscribers() : void
	{
		$subscriberType = 'FooSubscriber';

		$event = $this->mockEvent();
		$eventType = get_class($event);

		$resolver = new EventSubscribersResolver();
		$resolver->registerSubscriber($eventType, $subscriberType);
		$resolver->registerSubscriber($eventType, $subscriberType);

		Assert::same([$subscriberType, $subscriberType], $resolver->resolve($event));
	}



	/**
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent() : IDomainEvent
	{
		$mock = Mockery::mock(IDomainEvent::class);

		return $mock;
	}

}

(new EventSubscribersResolverTest())->run();
