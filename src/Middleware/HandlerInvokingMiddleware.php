<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\IHandlerInvoker;
use Damejidlo\MessageBus\Handling\IHandlerProvider;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;



final class HandlerInvokingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var IHandlerProvider
	 */
	private $handlerProvider;

	/**
	 * @var IHandlerInvoker
	 */
	private $handlerInvoker;



	public function __construct(IHandlerProvider $handlerProvider, IHandlerInvoker $handlerInvoker)
	{
		$this->handlerProvider = $handlerProvider;
		$this->handlerInvoker = $handlerInvoker;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		/** @var HandlerType $handlerType */
		$handlerType = $context->getByType(HandlerType::class);

		$handler = $this->handlerProvider->get($handlerType);

		return $this->handlerInvoker->invoke($handler, $message);
	}

}
