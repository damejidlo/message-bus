<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class CommandHasIncorrectNameHandler implements ICommandHandler
{

	/**
	 * @param IncorrectName $command
	 */
	public function handle(IncorrectName $command) : void
	{
	}

}
