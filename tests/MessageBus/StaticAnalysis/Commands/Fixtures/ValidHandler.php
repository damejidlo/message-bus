<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class ValidHandler implements ICommandHandler
{

	public function handle(ValidCommand $command) : void
	{
	}

}
