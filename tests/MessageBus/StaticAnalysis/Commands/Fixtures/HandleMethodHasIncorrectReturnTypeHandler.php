<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodHasIncorrectReturnTypeHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 * @return string
	 */
	public function handle(ValidCommand $command) : string
	{
		return '';
	}

}
