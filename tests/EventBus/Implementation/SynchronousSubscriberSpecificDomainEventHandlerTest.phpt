<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\EventSubscriberNotFoundException;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventSubscriber;
use Damejidlo\EventBus\IEventSubscriberProvider;
use Damejidlo\EventBus\Implementation\SynchronousSubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class SynchronousSubscriberSpecificDomainEventHandlerTest extends DjTestCase
{

	private const SUBSCRIBER_TYPE = 'Subscriber';



	public function testHandleSucceeds() : void
	{
		$event = $this->mockEvent();

		$subscriber = $this->mockEventSubscriber();

		$subscriberProvider = $this->mockEventSubscriberProvider();
		$subscriberProvider->shouldReceive('getByType')->with(self::SUBSCRIBER_TYPE)->andReturn($subscriber);

		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();

		$commonMiddleware = $this->mockMiddleware();

		$middleware = new SynchronousSubscriberSpecificDomainEventHandler(
			$subscriberProvider,
			$middlewareCallbackChainCreator
		);
		$middleware->appendMiddleware($commonMiddleware);

		$callbackChainCalled = FALSE;
		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->withArgs(
				function (array $actualMiddleware, \Closure $endChainWithCallback) use (
					$commonMiddleware,
					$subscriberSpecificDomainEvent
				) : bool {
					$endChainWithCallback($subscriberSpecificDomainEvent);
					Assert::same([$commonMiddleware], $actualMiddleware);

					return TRUE;
				}
			)->andReturn(
				function (SubscriberSpecificDomainEvent $message) use ($subscriberSpecificDomainEvent, &$callbackChainCalled) : void {
					$callbackChainCalled = TRUE;
					Assert::same($subscriberSpecificDomainEvent, $message);
				}
			);

		// expectations
		$subscriber->shouldReceive('handle')->once()->with($event);

		Assert::noError(function () use ($middleware, $subscriberSpecificDomainEvent) : void {
			$middleware->handle($subscriberSpecificDomainEvent);
		});
	}



	public function testHandleFails() : void
	{
		$event = $this->mockEvent();

		$subscriberProvider = $this->mockEventSubscriberProvider();
		$subscriberProvider->shouldReceive('getByType')->with(self::SUBSCRIBER_TYPE)->andThrow(EventSubscriberNotFoundException::class);

		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();

		$middleware = new SynchronousSubscriberSpecificDomainEventHandler(
			$subscriberProvider,
			$middlewareCallbackChainCreator
		);

		$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);
		Assert::exception(function () use ($middleware, $subscriberSpecificDomainEvent) : void {
			$middleware->handle($subscriberSpecificDomainEvent);
		}, EventSubscriberNotFoundException::class);
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
	 * @return IEventSubscriber|MockInterface
	 */
	private function mockEventSubscriber() : IEventSubscriber
	{
		$mock = Mockery::mock(IEventSubscriber::class);

		return $mock;
	}



	/**
	 * @return IEventSubscriberProvider|MockInterface
	 */
	private function mockEventSubscriberProvider() : IEventSubscriberProvider
	{
		$mock = \Mockery::mock(IEventSubscriberProvider::class);

		return $mock;
	}



	/**
	 * @return MiddlewareCallbackChainCreator|MockInterface
	 */
	private function mockMiddlewareCallbackChainCreator() : MiddlewareCallbackChainCreator
	{
		$mock = Mockery::mock(MiddlewareCallbackChainCreator::class);

		return $mock;
	}



	/**
	 * @return IMessageBusMiddleware|MockInterface
	 */
	private function mockMiddleware() : IMessageBusMiddleware
	{
		$mock = Mockery::mock(IMessageBusMiddleware::class);

		return $mock;
	}

}

(new SynchronousSubscriberSpecificDomainEventHandlerTest())->run();
