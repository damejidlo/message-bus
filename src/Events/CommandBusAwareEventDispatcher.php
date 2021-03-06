<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Events;

use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;



class CommandBusAwareEventDispatcher implements IEventDispatcher
{

	/**
	 * @var IsCurrentlyHandlingAwareMiddleware
	 */
	private $isCurrentlyHandlingAwareMiddleware;

	/**
	 * @var InMemoryEventQueue
	 */
	private $eventQueue;

	/**
	 * @var IEventDispatcher
	 */
	private $delegate;



	public function __construct(
		IsCurrentlyHandlingAwareMiddleware $isCurrentlyHandlingAwareMiddleware,
		InMemoryEventQueue $eventQueue,
		IEventDispatcher $delegate
	) {
		$this->isCurrentlyHandlingAwareMiddleware = $isCurrentlyHandlingAwareMiddleware;
		$this->eventQueue = $eventQueue;
		$this->delegate = $delegate;
	}



	public function dispatch(IEvent $event) : void
	{
		if ($this->isCurrentlyHandlingAwareMiddleware->isHandling()) {
			$this->eventQueue->enqueue($event);
		} else {
			$this->delegate->dispatch($event);
		}
	}

}
