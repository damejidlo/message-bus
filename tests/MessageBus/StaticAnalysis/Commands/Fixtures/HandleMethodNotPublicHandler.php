<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodNotPublicHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	protected function handle(ValidCommand $command) : void
	{
	}

}
