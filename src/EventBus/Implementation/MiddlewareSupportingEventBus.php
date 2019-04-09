<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Implementation;

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventBus;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;



final class MiddlewareSupportingEventBus implements IEventBus
{

	/**
	 * @var MiddlewareCallbackChainCreator
	 */
	private $middlewareCallbackChainCreator;

	/**
	 * @var IMessageBusMiddleware[]
	 */
	private $middleware = [];

	/**
	 * @var \Closure|NULL
	 */
	private $cachedCallback = NULL;



	public function __construct(MiddlewareCallbackChainCreator $middlewareCallbackChainCreator)
	{
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator;
	}



	public function appendMiddleware(IMessageBusMiddleware $middleware) : void
	{
		$this->middleware[] = $middleware;

		$this->cachedCallback = NULL;
	}



	public function handle(IDomainEvent $event) : void
	{
		$callback = $this->getCachedCallback();

		$callback($event);
	}



	private function getCachedCallback() : \Closure
	{
		if ($this->cachedCallback === NULL) {
			$this->cachedCallback = $this->createMiddlewareCallback();
		}

		return $this->cachedCallback;
	}



	private function createMiddlewareCallback() : \Closure
	{
		$endChainWithCallback = function (IDomainEvent $event) : void {
		};

		return $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);
	}

}
