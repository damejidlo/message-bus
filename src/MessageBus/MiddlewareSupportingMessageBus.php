<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



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
	 * @var MiddlewareCallback|NULL
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
	public function handle(IMessage $message, MiddlewareContext $context)
	{
		$callback = $this->getCachedCallback();

		return $callback($message, $context);
	}



	private function getCachedCallback() : MiddlewareCallback
	{
		if ($this->cachedCallback === NULL) {
			$this->cachedCallback = $this->createMiddlewareCallback();
		}

		return $this->cachedCallback;
	}



	private function createMiddlewareCallback() : MiddlewareCallback
	{
		$endChainWithCallback = MiddlewareCallback::empty();

		return $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);
	}

}
