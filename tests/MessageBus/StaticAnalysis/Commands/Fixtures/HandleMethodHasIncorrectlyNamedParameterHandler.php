<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodHasIncorrectlyNamedParameterHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $foo
	 */
	public function handle(ValidCommand $foo) : void
	{
	}

}
