<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param string $event
	 */
	public function handle(string $event) : void
	{
	}

}
