<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class MessageBusTest extends DjTestCase
{

	public function testHandleWithCorrectOrder() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MessageBus($middlewareCallbackChainCreator);

		$message = $this->mockMessage();

		$middleware = $this->mockMiddleware();
		$messageBus->appendMiddleware($middleware);

		$callbackChainCalled = FALSE;
		$result = 'some-result';

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->withArgs(function (array $actualMiddleware, MiddlewareCallback $endChainWithCallback) use ($middleware) : bool {
				Assert::same([$middleware], $actualMiddleware);
				return TRUE;
			})->andReturn(MiddlewareCallback::fromClosure(function (IMessage $actualMessage) use ($message, &$callbackChainCalled, $result) {
				$callbackChainCalled = TRUE;
				Assert::same($message, $actualMessage);

				return $result;
			}));

		$actualResult = $messageBus->handle($message, MiddlewareContext::empty());
		Assert::same($result, $actualResult);

		Assert::true($callbackChainCalled);
	}



	public function testHandleFails() : void
	{
		$middlewareCallbackChainCreator = $this->mockMiddlewareCallbackChainCreator();
		$messageBus = new MessageBus($middlewareCallbackChainCreator);

		$exception = new \Exception();

		$message = $this->mockMessage();

		// expectations
		$middlewareCallbackChainCreator->shouldReceive('create')->once()
			->andReturn(MiddlewareCallback::fromClosure(function (IMessage $actualMessage) use ($exception) : void {
				throw $exception;
			}));

		$actualException = Assert::exception(function () use ($messageBus, $message) : void {
			$messageBus->handle($message, MiddlewareContext::empty());
		}, \Exception::class);
		Assert::same($exception, $actualException);
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
	 * @return IMessage|MockInterface
	 */
	private function mockMessage() : IMessage
	{
		$mock = Mockery::mock(IMessage::class);

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



(new MessageBusTest())->run();
