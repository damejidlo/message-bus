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
	 * @var ?MiddlewareCallback
	 */
	private $cachedCallback = NULL;



	/**
	 * @param IMessageBusMiddleware[] $middleware
	 * @param MiddlewareCallbackChainCreator|null $middlewareCallbackChainCreator
	 */
	public function __construct(array $middleware, ?MiddlewareCallbackChainCreator $middlewareCallbackChainCreator = NULL)
	{
		$this->middleware = $middleware;
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator ?? new MiddlewareCallbackChainCreator();
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context)
	{
		$callback = $this->getMiddlewareCallback();

		return $callback($message, $context);
	}



	private function getMiddlewareCallback() : MiddlewareCallback
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
