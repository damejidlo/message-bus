<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



interface IMessageBusMiddleware
{

	/**
	 * @param IMessage $message typically a command or an event
	 * @param MiddlewareContext $context
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
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback);

}
