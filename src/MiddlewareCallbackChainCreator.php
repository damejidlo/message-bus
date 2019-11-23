<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class MiddlewareCallbackChainCreator
{

	/**
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallback $endChainWithCallback
	 * @return MiddlewareCallback
	 */
	public static function create(array $middleware, MiddlewareCallback $endChainWithCallback) : MiddlewareCallback
	{
		return self::createMiddlewareCallback(0, $middleware, $endChainWithCallback);
	}



	/**
	 * @param int $index
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallback $endChainWithCallback
	 * @return MiddlewareCallback
	 */
	private static function createMiddlewareCallback(int $index, array $middleware, MiddlewareCallback $endChainWithCallback) : MiddlewareCallback
	{
		if (!array_key_exists($index, $middleware)) {
			$callback = function (IMessage $message, MiddlewareContext $context) use ($endChainWithCallback) {
				return $endChainWithCallback($message, $context);
			};

			return MiddlewareCallback::fromClosure($callback);
		}

		$callback = function (IMessage $message, MiddlewareContext $context) use ($index, $middleware, $endChainWithCallback) {
			$singleMiddleware = $middleware[$index];

			return $singleMiddleware->handle($message, $context, self::createMiddlewareCallback($index + 1, $middleware, $endChainWithCallback));
		};

		return MiddlewareCallback::fromClosure($callback);
	}

}
