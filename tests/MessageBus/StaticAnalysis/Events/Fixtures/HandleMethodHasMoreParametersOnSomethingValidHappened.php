<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasMoreParametersOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param mixed $foo
	 * @param mixed $bar
	 */
	public function handle($foo, $bar) : void
	{
	}

}
