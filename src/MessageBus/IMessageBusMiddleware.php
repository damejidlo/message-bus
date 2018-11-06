<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface IMessageBusMiddleware
{

	/**
	 * @param IBusMessage $message typically a command or an event
	 * @param \Closure $nextMiddlewareCallback takes IBusMessage argument and returns mixed
	 * @return mixed
	 *
	 * Implement your logic and invoke $nextMiddlewareCallback with message argument where needed.
	 * $nextMiddlewareCallback must be invoked within the method.
	 * Return the result of callback.
	 *
	 * Example:
	 *
	 * return $nextMiddlewareCallback($message);
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback);

}
