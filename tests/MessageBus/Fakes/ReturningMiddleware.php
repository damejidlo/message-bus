<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Fakes;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class ReturningMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var int
	 */
	private $returnValue;



	public function __construct(int $returnValue)
	{
		$this->returnValue = $returnValue;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		return $this->returnValue;
	}

}
