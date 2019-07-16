<?php
declare(strict_types = 1);

namespace Damejidlo\Events;

/**
 * Enqueues events that are raised for later dispatch.
 */
interface IEventDispatcher
{

	public function dispatch(IDomainEvent $event) : void;

}
