<?php
declare(strict_types = 1);

namespace Damejidlo\Events;

/**
 * @internal
 */
interface IEventBus
{

	/**
	 * @param IEvent $event
	 *
	 * @internal
	 *
	 * Should not be used directly!
	 * @see IEventDispatcher instead for event dispatching
	 *
	 * Should not fail, throws no exceptions.
	 */
	public function handle(IEvent $event) : void;

}
