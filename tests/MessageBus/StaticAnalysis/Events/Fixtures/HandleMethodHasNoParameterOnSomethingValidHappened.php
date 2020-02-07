<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasNoParameterOnSomethingValidHappened implements IEventSubscriber
{

	public function handle() : void
	{
	}

}
