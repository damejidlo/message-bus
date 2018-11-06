<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Nette\SmartObject;



/**
 * This middleware is aware whether is it currently handling a message or not.
 */
class IsCurrentlyHandlingAwareMiddleware implements IMessageBusMiddleware
{

	use SmartObject;

	/**
	 * @var bool
	 */
	private $isHandling = FALSE;



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$this->isHandling = TRUE;

		try {
			return $nextMiddlewareCallback($message);
		} finally {
			$this->isHandling = FALSE;
		}
	}



	public function isHandling() : bool
	{
		return $this->isHandling;
	}

}
