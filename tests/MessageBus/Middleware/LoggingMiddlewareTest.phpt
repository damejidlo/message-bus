<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Implementation\MessageHashCalculator;
use Damejidlo\MessageBus\Middleware\LoggingMiddleware;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Tester\Assert;



class LoggingMiddlewareTest extends DjTestCase
{

	private const CALLBACK_RETURN_VALUE = 1;
	private const MESSAGE_HASH = 'message-hash';



	public function testHandleSucceedsWithCommand() : void
	{
		$logger = $this->mockLogger();
		$hashCalculator = $this->mockMessageHashCalculator();

		$middleware = new LoggingMiddleware($logger, $hashCalculator);

		$commandAsArray = [
			'someAttribute' => 123,
		];

		$command = $this->mockCommand();
		$command->shouldReceive('getLoggingContext')->andReturn($commandAsArray);

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = [
			'commandType' => get_class($command),
			'messageAttribute_someAttribute' => 123,
			'commandHash' => self::MESSAGE_HASH,
		];

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



	public function testHandleSucceedsEmptyArray() : void
	{
		$logger = $this->mockLogger();
		$hashCalculator = $this->mockMessageHashCalculator();

		$middleware = new LoggingMiddleware($logger, $hashCalculator);

		$command = $this->mockCommand();
		$command->shouldReceive('getLoggingContext')->andReturn([]);

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = [
			'commandType' => get_class($command),
			'commandHash' => self::MESSAGE_HASH,
		];

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
		$hashCalculator = $this->mockMessageHashCalculator();

		$middleware = new LoggingMiddleware($logger, $hashCalculator);

		$commandAsArray = [
			'someAttribute' => 123,
		];

		$command = $this->mockCommand();
		$command->shouldReceive('getLoggingContext')->andReturn($commandAsArray);

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = [
			'commandType' => get_class($command),
			'messageAttribute_someAttribute' => 123,
			'commandHash' => self::MESSAGE_HASH,
		];

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
		$hashCalculator = $this->mockMessageHashCalculator();

		$middleware = new LoggingMiddleware($logger, $hashCalculator);

		$eventAsArray = [
			'someAttribute' => 123,
		];

		$event = $this->mockEvent();
		$event->shouldReceive('getLoggingContext')->andReturn($eventAsArray);

		$nextMiddlewareCallbackCalled = FALSE;

		$expectedContext = [
			'eventType' => get_class($event),
			'messageAttribute_someAttribute' => 123,
			'eventHash' => self::MESSAGE_HASH,
		];

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
		$hashCalculator = $this->mockMessageHashCalculator();

		$middleware = new LoggingMiddleware($logger, $hashCalculator);

		$eventAsArray = [
			'someAttribute' => 123,
		];

		$event = $this->mockEvent();
		$event->shouldReceive('getLoggingContext')->andReturn($eventAsArray);

		$nextMiddlewareCallbackCalled = FALSE;

		$exceptionMessage = 'some message';
		$exception = new \Exception($exceptionMessage);

		$expectedContext = [
			'eventType' => get_class($event),
			'messageAttribute_someAttribute' => 123,
			'eventHash' => self::MESSAGE_HASH,
		];

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
	 * @return MessageHashCalculator|MockInterface
	 */
	private function mockMessageHashCalculator() : MessageHashCalculator
	{
		$mock = \Mockery::mock(MessageHashCalculator::class);
		$mock->shouldReceive('calculateHash')->withArgs([IBusMessage::class])->andReturn(self::MESSAGE_HASH);

		return $mock;
	}

}



(new LoggingMiddlewareTest())->run();
