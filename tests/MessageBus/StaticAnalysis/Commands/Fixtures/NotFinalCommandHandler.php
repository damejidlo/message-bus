<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class NotFinalCommandHandler implements ICommandHandler
{

	/**
	 * @param NotFinalCommand $command
	 */
	public function handle(NotFinalCommand $command) : void
	{
	}

}
