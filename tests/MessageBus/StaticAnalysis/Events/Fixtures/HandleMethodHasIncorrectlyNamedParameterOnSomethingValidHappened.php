<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $foo
	 */
	public function handle(SomethingValidHappenedEvent $foo) : void
	{
	}

}
