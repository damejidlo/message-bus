<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodHasNoParameterHandler implements ICommandHandler
{

	public function handle() : void
	{
	}

}
