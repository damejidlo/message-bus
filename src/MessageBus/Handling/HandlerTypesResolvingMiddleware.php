<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class HandlerTypesResolvingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var IHandlerTypesResolver
	 */
	private $resolver;



	public function __construct(IHandlerTypesResolver $resolver)
	{
		$this->resolver = $resolver;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$messageType = MessageType::fromMessage($message);
		$handlerTypes = $this->resolver->resolve($messageType);

		$context = $context->withValueStoredByType($handlerTypes);

		return $nextMiddlewareCallback($message, $context);
	}

}
