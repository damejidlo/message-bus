<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class MessageBus implements IMessageBus
{

	/**
	 * @var IMessageBusMiddleware[]
	 */
	private $middleware = [];

	/**
	 * @var ?MiddlewareCallback
	 */
	private $cachedCallback = NULL;



	public function __construct(IMessageBusMiddleware ...$middleware)
	{
		$this->middleware = $middleware;
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

		return MiddlewareCallbackChainCreator::create($this->middleware, $endChainWithCallback);
	}

}
