<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class MessageWithContextRecordingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var mixed[]
	 */
	private $items = [];



	public function handle(
		IMessage $message,
		MiddlewareContext $context,
		MiddlewareCallback $nextMiddlewareCallback
	) : void {
		$this->items[] = [
			'message' => $message,
			'context' => $context,
		];

		$nextMiddlewareCallback($message, $context);
	}



	/**
	 * @return mixed[]
	 */
	public function release() : array
	{
		$result = $this->items;
		$this->items = [];

		return $result;
	}

}
