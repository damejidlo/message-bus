<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodHasParameterWithIncorrectTypeHandler implements ICommandHandler
{

	/**
	 * @param string $command
	 */
	public function handle(string $command) : void
	{
	}

}
