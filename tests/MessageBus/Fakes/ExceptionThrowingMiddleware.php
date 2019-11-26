<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Fakes;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class ExceptionThrowingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var \Exception
	 */
	private $exception;



	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		throw $this->exception;
	}

}
