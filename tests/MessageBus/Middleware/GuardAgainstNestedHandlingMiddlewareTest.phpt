<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\Middleware\AlreadyHandlingOtherMessageException;
use Damejidlo\MessageBus\Middleware\GuardAgainstNestedHandlingMiddleware;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class GuardAgainstNestedHandlingMiddlewareTest extends DjTestCase
{

	public function testCallsNextMiddlewareWithCorrectMessageAndReturnsCorrectResult() : void
	{
		$middleware = new GuardAgainstNestedHandlingMiddleware(
			$this->mockIsCurrentlyHandlingAwareMiddleware(FALSE)
		);

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



	public function testHandleSucceedsWhenNotCurrentlyHandling() : void
	{
		$middleware = new GuardAgainstNestedHandlingMiddleware(
			$this->mockIsCurrentlyHandlingAwareMiddleware(FALSE)
		);

		$message = $this->mockMessage();

		Assert::noError(
			function () use ($middleware, $message) : void {
				$middleware->handle(
					$message,
					MiddlewareContext::empty(),
					MiddlewareCallback::empty()
				);
			}
		);
	}



	public function testHandleFailsWhenCurrentlyHandling() : void
	{
		$middleware = new GuardAgainstNestedHandlingMiddleware(
			$this->mockIsCurrentlyHandlingAwareMiddleware(TRUE)
		);

		$message = $this->mockMessage();

		Assert::exception(
			function () use ($middleware, $message) : void {
				$middleware->handle(
					$message,
					MiddlewareContext::empty(),
					MiddlewareCallback::empty()
				);
			},
			AlreadyHandlingOtherMessageException::class
		);
	}



	/**
	 * @param bool $isHandling
	 * @return IsCurrentlyHandlingAwareMiddleware|MockInterface
	 */
	private function mockIsCurrentlyHandlingAwareMiddleware(bool $isHandling) : IsCurrentlyHandlingAwareMiddleware
	{
		$mock = Mockery::mock(IsCurrentlyHandlingAwareMiddleware::class);
		$mock->shouldReceive('isHandling')->andReturn($isHandling);

		return $mock;
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



(new GuardAgainstNestedHandlingMiddlewareTest())->run();
