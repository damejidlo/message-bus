<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class SplitByHandlerTypeMiddleware implements IMessageBusMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(IBusMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$handlerTypes = HandlerTypes::extractFrom($context);

		if ($handlerTypes->count() === 1) {
			$handlerType = $handlerTypes->getOne();
			return $nextMiddlewareCallback($message, $handlerType->saveTo($context));
		}

		foreach ($handlerTypes->toArray() as $handlerType) {
			$nextMiddlewareCallback($message, $handlerType->saveTo($context));
		}
	}

}
