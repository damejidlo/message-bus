<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class NotFinalEventOnSomethingInvalidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingInvalidHappenedEvent $event
	 */
	public function handle(SomethingInvalidHappenedEvent $event) : void
	{
	}

}
