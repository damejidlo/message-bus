<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Fakes\ExceptionThrowingMiddleware;
use DamejidloTests\MessageBus\Fakes\MiddlewareLog;
use DamejidloTests\MessageBus\Fakes\RecordingMiddleware;
use DamejidloTests\MessageBus\Fakes\ReturningMiddleware;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class MessageBusTest extends DjTestCase
{

	public function testHandleWithCorrectOrder() : void
	{
		$message = $this->mockMessage();
		$context = MiddlewareContext::empty();

		$log = new MiddlewareLog();
		$middleware1 = new RecordingMiddleware(1, $log);
		$middleware2 = new RecordingMiddleware(2, $log);
		$returnValue = 333;
		$middleware3 = new ReturningMiddleware($returnValue);

		$messageBus = new MessageBus(
			$middleware1,
			$middleware2,
			$middleware3
		);

		$actualResult = $messageBus->handle($message, $context);
		Assert::same($returnValue, $actualResult);

		Assert::same([1, 2], array_keys($log->middlewareCalled));

		Assert::same($message, $middleware1->getMessage());
		Assert::same($message, $middleware2->getMessage());
		Assert::same($context, $middleware1->getContext());
		Assert::same($context, $middleware2->getContext());
	}



	public function testHandleFails() : void
	{
		$exception = new \Exception();
		$middleware = new ExceptionThrowingMiddleware($exception);

		$messageBus = new MessageBus($middleware);

		$message = $this->mockMessage();

		$actualException = Assert::exception(function () use ($messageBus, $message) : void {
			$messageBus->handle($message, MiddlewareContext::empty());
		}, \Exception::class);
		Assert::same($exception, $actualException);
	}



	/**
	 * @return IMessage|MockInterface
	 */
	private function mockMessage() : IMessage
	{
		$mock = Mockery::mock(IMessage::class);

		return $mock;
	}

}



(new MessageBusTest())->run();
