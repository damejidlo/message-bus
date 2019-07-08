<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class SplitByHandlerTypeMiddleware implements IMessageBusMiddleware
{

	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		/** @var HandlerTypes $handlerTypes */
		$handlerTypes = $context->get(HandlerTypes::class);

		if ($handlerTypes->count() === 1) {
			$handlerType = $handlerTypes->getOne();
			$context = $context->withValueStoredByType($handlerType);

			return $nextMiddlewareCallback($message, $context);
		}

		foreach ($handlerTypes->toArray() as $handlerType) {
			$context = $context->withValueStoredByType($handlerType);

			$nextMiddlewareCallback($message, $context);
		}
	}

}
