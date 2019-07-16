<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class MessageBus implements IMessageBus
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
	 * @var MiddlewareCallback
	 */
	private $cachedCallback;



	/**
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallbackChainCreator|null $middlewareCallbackChainCreator
	 */
	public function __construct(array $middleware, ?MiddlewareCallbackChainCreator $middlewareCallbackChainCreator = NULL)
	{
		$this->middleware = $middleware;
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator ?? new MiddlewareCallbackChainCreator();

		$this->cachedCallback = $this->createMiddlewareCallback();
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context)
	{
		$callback = $this->cachedCallback;

		return $callback($message, $context);
	}



	private function createMiddlewareCallback() : MiddlewareCallback
	{
		$endChainWithCallback = MiddlewareCallback::empty();

		return $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);
	}

}
