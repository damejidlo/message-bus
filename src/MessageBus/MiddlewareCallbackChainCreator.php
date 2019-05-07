<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;



class MiddlewareCallbackChainCreator
{

	/**
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallback $endChainWithCallback
	 * @return MiddlewareCallback
	 */
	public function create(array $middleware, MiddlewareCallback $endChainWithCallback) : MiddlewareCallback
	{
		return $this->createMiddlewareCallback(0, $middleware, $endChainWithCallback);
	}



	/**
	 * @param int $index
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallback $endChainWithCallback
	 * @return MiddlewareCallback
	 */
	private function createMiddlewareCallback(int $index, array $middleware, MiddlewareCallback $endChainWithCallback) : MiddlewareCallback
	{
		if (!array_key_exists($index, $middleware)) {
			$callback = function (IBusMessage $message) use ($endChainWithCallback) {
				return $endChainWithCallback($message);
			};

			return MiddlewareCallback::fromClosure($callback);
		}

		$callback = function (IBusMessage $message) use ($index, $middleware, $endChainWithCallback) {
			$singleMiddleware = $middleware[$index];

			return $singleMiddleware->handle($message, $this->createMiddlewareCallback($index + 1, $middleware, $endChainWithCallback));
		};

		return MiddlewareCallback::fromClosure($callback);
	}

}
