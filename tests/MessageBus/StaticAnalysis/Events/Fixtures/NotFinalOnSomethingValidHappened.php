<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



class NotFinalOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}
