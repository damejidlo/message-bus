<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use Damejidlo\MessageBus\Middleware\LoggingMiddleware;
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



	public function testHandleSucceedsWithCommand() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$command = $this->mockCommand();

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = self::MESSAGE_CONTEXT;

		// expectations
		$logger->shouldReceive('info')->once()->with('Command handling started.', $expectedContext);
		$logger->shouldReceive('info')->once()->with('Command handling ended successfully.', $expectedContext);

		$result = $middleware->handle($command, function (ICommand $command) use (&$nextMiddlewareCallbackCalled) {
			$nextMiddlewareCallbackCalled = TRUE;
			return self::CALLBACK_RETURN_VALUE;
		});

		Assert::same(self::CALLBACK_RETURN_VALUE, $result);
		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleFailsWithCommand() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$command = $this->mockCommand();

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = self::MESSAGE_CONTEXT;

		$expectedErrorContext = $expectedContext;
		$expectedErrorContext['exceptionType'] = 'Exception';
		$expectedErrorContext['exceptionMessage'] = $exceptionMessage;

		// expectations
		$logger->shouldReceive('info')->once()->with('Command handling started.', $expectedContext);
		$logger->shouldReceive('warning')->once()->with('Command handling ended with error: some message', $expectedErrorContext);

		$actualException = Assert::exception(function () use ($middleware, $command, &$nextMiddlewareCallbackCalled, $exception) : void {
			$middleware->handle($command, function (ICommand $command) use (&$nextMiddlewareCallbackCalled, $exception) : void {
				$nextMiddlewareCallbackCalled = TRUE;
				throw $exception;
			});
		}, \Exception::class);
		Assert::same($exception, $actualException);

		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleSucceedsWithEvent() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$event = $this->mockEvent();

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = self::MESSAGE_CONTEXT;

		// expectations
		$logger->shouldReceive('info')->once()->with('Event handling started.', $expectedContext);
		$logger->shouldReceive('info')->once()->with('Event handling ended successfully.', $expectedContext);

		$result = $middleware->handle($event, function (IDomainEvent $event) use (&$nextMiddlewareCallbackCalled) {
			$nextMiddlewareCallbackCalled = TRUE;
			return self::CALLBACK_RETURN_VALUE;
		});

		Assert::same(self::CALLBACK_RETURN_VALUE, $result);
		Assert::true($nextMiddlewareCallbackCalled);
	}



	public function testHandleFailsWithEvent() : void
	{
		$logger = $this->mockLogger();
		$messageContextResolver = $this->mockMessageContextResolver();

		$middleware = new LoggingMiddleware($logger, NULL, $messageContextResolver);

		$event = $this->mockEvent();

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = self::MESSAGE_CONTEXT;

		$expectedErrorContext = $expectedContext;
		$expectedErrorContext['exceptionType'] = 'Exception';
		$expectedErrorContext['exceptionMessage'] = $exceptionMessage;

		// expectations
		$logger->shouldReceive('info')->once()->with('Event handling started.', $expectedContext);
		$logger->shouldReceive('warning')->once()->with('Event handling ended with error: some message', $expectedErrorContext);

		Assert::exception(function () use ($middleware, $event, &$nextMiddlewareCallbackCalled, $exception) : void {
			$middleware->handle($event, function (IDomainEvent $event) use (&$nextMiddlewareCallbackCalled, $exception) : void {
				$nextMiddlewareCallbackCalled = TRUE;
				throw $exception;
			});
		}, \Exception::class);

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
	 * @return ICommand|MockInterface
	 */
	private function mockCommand() : ICommand
	{
		$mock = Mockery::mock(ICommand::class);

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



(new LoggingMiddlewareTest())->run();
