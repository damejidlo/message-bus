<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;



/**
 * This middleware is aware whether is it currently handling a message or not.
 */
class IsCurrentlyHandlingAwareMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var bool
	 */
	private $isHandling = FALSE;



	/**
	 * @inheritdoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$this->isHandling = TRUE;

		try {
			return $nextMiddlewareCallback($message, $context);
		} finally {
			$this->isHandling = FALSE;
		}
	}



	public function isHandling() : bool
	{
		return $this->isHandling;
	}

}
