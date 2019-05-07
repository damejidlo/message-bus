<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\EventSubscriberNotFoundException;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventSubscribersResolver;
use Damejidlo\EventBus\ISubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\Middleware\SubscribersResolvingMiddleware;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class SubscribersResolvingMiddlewareTest extends DjTestCase
{

	private const SUBSCRIBER_TYPE_1 = 'Subscriber1';
	private const SUBSCRIBER_TYPE_2 = 'Subscriber2';



	public function testHandleSucceeds() : void
	{
		$event = $this->mockEvent();

		$subscribersResolver = $this->mockEventSubscribersResolver();
		$subscribersResolver->shouldReceive('resolve')->andReturn([self::SUBSCRIBER_TYPE_1, self::SUBSCRIBER_TYPE_2]);

		$subscriberSpecificDomainEventHandler = $this->mockSubscriberSpecificDomainEventHandler();
		$subscriberSpecificDomainEventHandler->shouldReceive('handle')
			->withArgs(
				function (SubscriberSpecificDomainEvent $message) use ($event) : bool {
					return $message->getSubscriberType() === self::SUBSCRIBER_TYPE_1 && $message->getEvent() === $event;
				}
			)
			->once();
		$subscriberSpecificDomainEventHandler->shouldReceive('handle')
			->withArgs(
				function (SubscriberSpecificDomainEvent $message) use ($event) : bool {
					return $message->getSubscriberType() === self::SUBSCRIBER_TYPE_2 && $message->getEvent() === $event;
				}
			)
			->once();

		$middleware = new SubscribersResolvingMiddleware(
			$subscribersResolver,
			$subscriberSpecificDomainEventHandler
		);

		Assert::noError(
			function () use ($middleware, $event) : void {
				$middleware->handle(
					$event,
					MiddlewareContext::empty(),
					MiddlewareCallback::empty()
				);
			}
		);
	}



	public function testHandleFails() : void
	{
		$event = $this->mockEvent();

		$subscribersResolver = $this->mockEventSubscribersResolver();
		$subscribersResolver->shouldReceive('resolve')->andReturn([self::SUBSCRIBER_TYPE_1, self::SUBSCRIBER_TYPE_2]);

		$subscriberSpecificDomainEventHandler = $this->mockSubscriberSpecificDomainEventHandler();
		$subscriberSpecificDomainEventHandler->shouldReceive('handle')
			->withArgs(
				function (SubscriberSpecificDomainEvent $message) use ($event) : bool {
					return $message->getSubscriberType() === self::SUBSCRIBER_TYPE_1 && $message->getEvent() === $event;
				}
			)
			->andThrow(EventSubscriberNotFoundException::class)
			->once();

		$middleware = new SubscribersResolvingMiddleware(
			$subscribersResolver,
			$subscriberSpecificDomainEventHandler
		);

		Assert::exception(
			function () use ($middleware, $event) : void {
				$middleware->handle(
					$event,
					MiddlewareContext::empty(),
					MiddlewareCallback::empty()
				);
			},
			EventSubscriberNotFoundException::class
		);
	}



	/**
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent() : IDomainEvent
	{
		$mock = Mockery::mock(IDomainEvent::class);

		return $mock;
	}



	/**
	 * @return IEventSubscribersResolver|MockInterface
	 */
	private function mockEventSubscribersResolver() : IEventSubscribersResolver
	{
		$mock = Mockery::mock(IEventSubscribersResolver::class);

		return $mock;
	}



	/**
	 * @return ISubscriberSpecificDomainEventHandler|MockInterface
	 */
	private function mockSubscriberSpecificDomainEventHandler() : ISubscriberSpecificDomainEventHandler
	{
		$mock = \Mockery::mock(ISubscriberSpecificDomainEventHandler::class);

		return $mock;
	}

}



(new SubscribersResolvingMiddlewareTest())->run();
