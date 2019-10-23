<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



class NotFinalHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	public function handle(ValidCommand $command) : void
	{
	}

}
