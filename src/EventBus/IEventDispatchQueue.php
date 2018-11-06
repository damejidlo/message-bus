<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

/**
 * Enqueues events that are raised for later dispatch.
 */
interface IEventDispatchQueue
{

	/**
	 * @param IDomainEvent $event
	 */
	public function enqueue(IDomainEvent $event) : void;

}
