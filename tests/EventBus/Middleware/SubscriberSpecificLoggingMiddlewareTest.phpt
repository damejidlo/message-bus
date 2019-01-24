<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\Middleware\SubscriberSpecificLoggingMiddleware;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Tester\Assert;



class SubscriberSpecificLoggingMiddlewareTest extends DjTestCase
{

	private const CALLBACK_RETURN_VALUE = 1;
	private const SUBSCRIBER_TYPE = 'Subscriber';
	private const MESSAGE_CONTEXT = [
		'someAttribute' => 'someValue',
	];



	public function testHandleSucceeds() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new SubscriberSpecificLoggingMiddleware($logger, $messageContextResolver);

		$event = $this->mockEvent();
		$message = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = self::MESSAGE_CONTEXT;

		// expectations
		$logger->shouldReceive('info')->once()->with('Event handling in subscriber started.', $expectedContext);
		$logger->shouldReceive('info')->once()->with('Event handling in subscriber ended successfully.', $expectedContext);

		$result = $middleware->handle($message, function () use (&$nextMiddlewareCallbackCalled) {
			$nextMiddlewareCallbackCalled = TRUE;
			return self::CALLBACK_RETURN_VALUE;
		});

		Assert::same(self::CALLBACK_RETURN_VALUE, $result);
		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleFails() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new SubscriberSpecificLoggingMiddleware($logger, $messageContextResolver);

		$event = $this->mockEvent();
		$message = new SubscriberSpecificDomainEvent($event, self::SUBSCRIBER_TYPE);

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = self::MESSAGE_CONTEXT;

		$expectedErrorContext = $expectedContext;
		$expectedErrorContext['exceptionType'] = 'Exception';
		$expectedErrorContext['exceptionMessage'] = $exceptionMessage;

		// expectations
		$logger->shouldReceive('info')->once()->with('Event handling in subscriber started.', $expectedContext);
		$logger->shouldReceive('error')->once()->with('Event handling in subscriber ended with error: some message', $expectedErrorContext);

		Assert::exception(function () use ($middleware, $message, &$nextMiddlewareCallbackCalled, $exception) : void {
			$middleware->handle($message, function () use (&$nextMiddlewareCallbackCalled, $exception) : void {
				$nextMiddlewareCallbackCalled = TRUE;
				throw $exception;
			});
		}, \Exception::class, $exceptionMessage);

		Assert::true($nextMiddlewareCallbackCalled);
	}



	/**
	 * @return LoggerInterface|MockInterface
	 */
	private function mockLogger() : LoggerInterface
	{
		$mock = Mockery::mock(LoggerInterface::class);

		return $mock;
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
	 * @return MessageContextResolver|MockInterface
	 */
	private function mockMessageContextResolver() : MessageContextResolver
	{
		$mock = \Mockery::mock(MessageContextResolver::class);
		$mock->shouldReceive('getContext')->andReturn(self::MESSAGE_CONTEXT);

		return $mock;
	}

}



(new SubscriberSpecificLoggingMiddlewareTest())->run();
