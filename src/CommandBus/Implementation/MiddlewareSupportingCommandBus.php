<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Implementation;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\ICommandBus;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;



final class MiddlewareSupportingCommandBus implements ICommandBus
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

	/**
	 * @param MiddlewareCallbackChainCreator $middlewareCallbackChainCreator
	 */
	public function __construct(MiddlewareCallbackChainCreator $middlewareCallbackChainCreator)
	{
		$this->middlewareCallbackChainCreator = $middlewareCallbackChainCreator;
	}



	/**
	 * @param IMessageBusMiddleware $middleware
	 */
	public function appendMiddleware(IMessageBusMiddleware $middleware) : void
	{
		$this->middleware[] = $middleware;

		$this->cachedCallback = NULL;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(ICommand $command) : ?NewEntityId
	{
		$callback = $this->getCachedCallback();

		return $callback($command);
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
		$endChainWithCallback = function (ICommand $command) : void {};

		return $this->middlewareCallbackChainCreator->create($this->middleware, $endChainWithCallback);
	}

}
