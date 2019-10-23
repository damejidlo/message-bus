<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodNotPublicOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	protected function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}
