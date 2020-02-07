<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class EventHasIncorrectNameOnIncorrectName implements IEventSubscriber
{

	/**
	 * @param IncorrectName $event
	 */
	public function handle(IncorrectName $event) : void
	{
	}

}
