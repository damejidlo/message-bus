<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\Events\IEventDispatcher;
use Damejidlo\MessageBus\Events\InMemoryEventQueue;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;



class EventDispatchingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var InMemoryEventQueue
	 */
	private $eventQueue;

	/**
	 * @var IEventDispatcher
	 */
	private $eventDispatcher;



	public function __construct(
		InMemoryEventQueue $eventQueue,
		IEventDispatcher $eventDispatcher
	) {
		$this->eventQueue = $eventQueue;
		$this->eventDispatcher = $eventDispatcher;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		try {
			$result = $nextMiddlewareCallback($message, $context);

			foreach ($this->eventQueue->releaseEvents() as $event) {
				$this->eventDispatcher->dispatch($event);
			}

			return $result;

		} finally {
			$this->eventQueue->releaseEvents();
		}
	}

}
