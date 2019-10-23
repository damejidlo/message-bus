<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Events;

/**
 * Enqueues events that are raised for later dispatch.
 */
interface IEventDispatcher
{

	public function dispatch(IEvent $event) : void;

}
