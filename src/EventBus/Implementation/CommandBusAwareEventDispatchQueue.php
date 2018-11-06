<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Implementation;

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventDispatchQueue;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use Nette\SmartObject;



class CommandBusAwareEventDispatchQueue implements IEventDispatchQueue
{

	use SmartObject;

	/**
	 * @var IsCurrentlyHandlingAwareMiddleware
	 */
	private $isCurrentlyHandlingAwareMiddleware;

	/**
	 * @var InMemoryEventQueue
	 */
	private $eventQueue;

	/**
	 * @var IEventDispatchQueue
	 */
	private $eventDispatchQueue;



	public function __construct(
		IsCurrentlyHandlingAwareMiddleware $isCurrentlyHandlingAwareMiddleware,
		InMemoryEventQueue $eventQueue,
		IEventDispatchQueue $eventDispatchQueue
	) {
		$this->isCurrentlyHandlingAwareMiddleware = $isCurrentlyHandlingAwareMiddleware;
		$this->eventQueue = $eventQueue;
		$this->eventDispatchQueue = $eventDispatchQueue;
	}



	/**
	 * @inheritdoc
	 */
	public function enqueue(IDomainEvent $event) : void
	{
		if ($this->isCurrentlyHandlingAwareMiddleware->isHandling()) {
			$this->eventQueue->enqueue($event);
		} else {
			// passthru
			$this->eventDispatchQueue->enqueue($event);
		}
	}

}
