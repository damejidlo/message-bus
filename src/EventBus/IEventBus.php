<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

/**
 * @internal
 */
interface IEventBus
{

	/**
	 * @param IDomainEvent $event
	 *
	 * @internal
	 *
	 * Should not be used directly!
	 * @see IEventDispatcher instead for event dispatching
	 *
	 * Should not fail, throws no exceptions.
	 */
	public function handle(IDomainEvent $event) : void;

}
