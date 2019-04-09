<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

final class MiddlewareSupportingMessageBus implements IMessageBus
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



	public function __construct(?MiddlewareCallbackChainCreator $middlewareCallbackChainCreator = NULL)
	{
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator ?? new MiddlewareCallbackChainCreator();
	}



	public function appendMiddleware(IMessageBusMiddleware $middleware) : void
	{
		$this->middleware[] = $middleware;

		$this->cachedCallback = NULL;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IBusMessage $message)
	{
		$callback = $this->getCachedCallback();

		return $callback($message);
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
		$endChainWithCallback = function (IBusMessage $message) : void {
		};

		return $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);
	}

}
