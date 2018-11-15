<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

class MiddlewareCallbackChainCreator
{

	/**
	 * @param IMessageBusMiddleware[] $middleware
	 * @param \Closure $endChainWithCallback
	 * @return \Closure
	 */
	public function create(array $middleware, \Closure $endChainWithCallback) : \Closure
	{
		return $this->createMiddlewareCallback(0, $middleware, $endChainWithCallback);
	}



	/**
	 * @param int $index
	 * @param IMessageBusMiddleware[] $middleware
	 * @param \Closure $endChainWithCallback
	 * @return \Closure
	 */
	private function createMiddlewareCallback(int $index, array $middleware, \Closure $endChainWithCallback) : \Closure
	{
		if (!array_key_exists($index, $middleware)) {
			return function (IBusMessage $message) use ($endChainWithCallback) {
				return $endChainWithCallback($message);
			};
		}

		return function (IBusMessage $message) use ($index, $middleware, $endChainWithCallback) {
			$singleMiddleware = $middleware[$index];

			return $singleMiddleware->handle($message, $this->createMiddlewareCallback($index + 1, $middleware, $endChainWithCallback));
		};
	}

}
