<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class IsCurrentlyHandlingAwareMiddlewareTest extends DjTestCase
{

	public function testCallsNextMiddlewareWithCorrectMessageAndReturnsCorrectResult() : void
	{
		$middleware = new IsCurrentlyHandlingAwareMiddleware();

		$message = $this->mockMessage();

		$nextMiddlewareWasCalled = FALSE;

		$result = 'some-result';

		$actualResult = $middleware->handle(
			$message,
			MiddlewareContext::empty(),
			MiddlewareCallback::fromClosure(function (IMessage $actualMessage) use ($message, &$nextMiddlewareWasCalled, $result) {
				Assert::same($message, $actualMessage);
				$nextMiddlewareWasCalled = TRUE;

				return $result;
			})
		);

		Assert::true($nextMiddlewareWasCalled);
		Assert::same($result, $actualResult);
	}



	public function testIsNotHandlingByDefault() : void
	{
		$middleware = new IsCurrentlyHandlingAwareMiddleware();

		Assert::false($middleware->isHandling());
	}



	public function testIsCurrentlyHandlingWhenCallingNextMiddlewareCallback() : void
	{
		$middleware = new IsCurrentlyHandlingAwareMiddleware();

		$message = $this->mockMessage();

		$middleware->handle(
			$message,
			MiddlewareContext::empty(),
			MiddlewareCallback::fromClosure(
				function (IMessage $actualMessage) use ($middleware) : void {
					Assert::true($middleware->isHandling());
				}
			)
		);
	}



	public function testIsNotCurrentlyHandlingWhenSuccessfullyFinishedCallingNextMiddlewareCallback() : void
	{
		$middleware = new IsCurrentlyHandlingAwareMiddleware();

		$message = $this->mockMessage();

		$middleware->handle(
			$message,
			MiddlewareContext::empty(),
			MiddlewareCallback::empty()
		);

		Assert::false($middleware->isHandling());
	}



	public function testIsNotCurrentlyHandlingWhenCallingNextMiddlewareCallbackFails() : void
	{
		$middleware = new IsCurrentlyHandlingAwareMiddleware();

		$message = $this->mockMessage();

		$exception = new \Exception();

		$actualException = Assert::exception(
			function () use ($middleware, $message, $exception) : void {
				$middleware->handle(
					$message,
					MiddlewareContext::empty(),
					MiddlewareCallback::fromClosure(function (IMessage $actualMessage) use ($exception) : void {
						throw $exception;
					})
				);
			},
			\Exception::class
		);

		Assert::same($exception, $actualException);

		Assert::false($middleware->isHandling());
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



(new IsCurrentlyHandlingAwareMiddlewareTest())->run();
