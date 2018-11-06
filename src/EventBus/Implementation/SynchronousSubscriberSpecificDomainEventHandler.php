<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Implementation;

use Damejidlo\EventBus\IEventSubscriberProvider;
use Damejidlo\EventBus\ISubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;



class SynchronousSubscriberSpecificDomainEventHandler implements ISubscriberSpecificDomainEventHandler
{

	/**
	 * @var IEventSubscriberProvider
	 */
	private $eventSubscriberProvider;

	/**
	 * @var MiddlewareCallbackChainCreator
	 */
	private $middlewareCallbackChainCreator;

	/**
	 * @var IMessageBusMiddleware[]
	 */
	private $middleware = [];



	public function __construct(
		IEventSubscriberProvider $eventSubscriberProvider,
		MiddlewareCallbackChainCreator $middlewareCallbackChainCreator
	) {
		$this->eventSubscriberProvider = $eventSubscriberProvider;
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator;
	}



	public function appendMiddleware(IMessageBusMiddleware $middleware) : void
	{
		$this->middleware[] = $middleware;
	}



	public function handle(SubscriberSpecificDomainEvent $message) : void
	{
		$subscriber = $this->eventSubscriberProvider->getByType($message->getSubscriberType());

		$endChainWithCallback = function (SubscriberSpecificDomainEvent $message) use ($subscriber) : void {
			$callback = [$subscriber, 'handle'];
			call_user_func($callback, $message->getEvent());
		};

		$callback = $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);

		$callback($message);
	}

}
