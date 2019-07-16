<?php declare(strict_types = 1);

namespace Damejidlo\Events;

final class SynchronousEventDispatcher implements IEventDispatcher
{

	/**
	 * @var IEventBus
	 */
	private $eventBus;



	public function __construct(IEventBus $eventBus)
	{
		$this->eventBus = $eventBus;
	}



	public function dispatch(IDomainEvent $event) : void
	{
		$this->eventBus->handle($event);
	}

}
