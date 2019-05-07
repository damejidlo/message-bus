<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;



interface IMessageBusMiddleware
{

	/**
	 * @param IBusMessage $message typically a command or an event
	 * @param MiddlewareCallback $nextMiddlewareCallback
	 * @return mixed
	 *
	 * Implement your logic and invoke $nextMiddlewareCallback where needed.
	 * $nextMiddlewareCallback must be invoked within the method.
	 * Return the result of callback.
	 *
	 * Example:
	 *
	 * return $nextMiddlewareCallback($message);
	 */
	public function handle(IBusMessage $message, MiddlewareCallback $nextMiddlewareCallback);

}
