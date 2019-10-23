<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class CommandNameDoesNotMatchHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	public function handle(ValidCommand $command) : void
	{
	}

}
