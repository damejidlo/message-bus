<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasIncorrectReturnTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 * @return string
	 */
	public function handle(SomethingValidHappenedEvent $event) : string
	{
		return '';
	}

}
