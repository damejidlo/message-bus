<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Middleware;

use Damejidlo\EventBus\IEventDispatchQueue;
use Damejidlo\EventBus\Implementation\InMemoryEventQueue;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;



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
	 * @var IEventDispatchQueue
	 */
	private $eventDispatchQueue;



	public function __construct(
		InMemoryEventQueue $eventQueue,
		IEventDispatchQueue $eventDispatchQueue
	) {
		$this->eventQueue = $eventQueue;
		$this->eventDispatchQueue = $eventDispatchQueue;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		try {
			$result = $nextMiddlewareCallback($message);

			foreach ($this->eventQueue->releaseEvents() as $event) {
				$this->eventDispatchQueue->enqueue($event);
			}

			return $result;

		} finally {
			$this->eventQueue->releaseEvents();
		}
	}

}
