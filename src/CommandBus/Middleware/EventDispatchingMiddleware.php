<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Middleware;

use Damejidlo\EventBus\IEventDispatcher;
use Damejidlo\EventBus\Implementation\InMemoryEventQueue;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



/**
 * If an event is raised during handling of a command, the event is dispatched only after the handler ended it's work.
 */
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
	public function handle(IBusMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
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
