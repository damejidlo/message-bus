<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use Damejidlo\MessageBus\Middleware\LoggingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Tester\Assert;



class LoggingMiddlewareTest extends DjTestCase
{

	private const CALLBACK_RETURN_VALUE = 1;
	private const MESSAGE_CONTEXT = [
		'someAttribute' => 'someValue',
	];



	public function testHandleSucceeds() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$message = $this->mockBusMessage();

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = self::MESSAGE_CONTEXT;

		// expectations
		$logger->shouldReceive('info')->once()->with('Message handling started.', $expectedContext);
		$logger->shouldReceive('info')->once()->with('Message handling ended successfully.', $expectedContext);

		$result = $middleware->handle(
			$message,
			MiddlewareContext::empty(),
			MiddlewareCallback::fromClosure(
				function (IBusMessage $message) use (&$nextMiddlewareCallbackCalled) {
					$nextMiddlewareCallbackCalled = TRUE;

					return self::CALLBACK_RETURN_VALUE;
				}
			)
		);

		Assert::same(self::CALLBACK_RETURN_VALUE, $result);
		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleFails() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$message = $this->mockBusMessage();

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = self::MESSAGE_CONTEXT;

		$expectedErrorContext = $expectedContext;
		$expectedErrorContext['exceptionType'] = 'Exception';
		$expectedErrorContext['exceptionMessage'] = $exceptionMessage;

		// expectations
		$logger->shouldReceive('info')->once()->with('Message handling started.', $expectedContext);
		$logger->shouldReceive('warning')->once()->with('Message handling ended with error: some message', $expectedErrorContext);

		$actualException = Assert::exception(
			function () use ($middleware, $message, &$nextMiddlewareCallbackCalled, $exception) : void {
				$middleware->handle(
					$message,
					MiddlewareContext::empty(),
					MiddlewareCallback::fromClosure(
						function (IBusMessage $message) use (&$nextMiddlewareCallbackCalled, $exception) : void {
							$nextMiddlewareCallbackCalled = TRUE;
							throw $exception;
						}
					)
				);
			},
			\Exception::class
		);
		Assert::same($exception, $actualException);

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
	 * @return IBusMessage|MockInterface
	 */
	private function mockBusMessage() : IBusMessage
	{
		$mock = Mockery::mock(IBusMessage::class);

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



(new LoggingMiddlewareTest())->run();
