<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;



final class HandleMethodHasMoreParametersHandler implements ICommandHandler
{

	/**
	 * @param mixed $foo
	 * @param mixed $bar
	 */
	public function handle($foo, $bar) : void
	{
	}

}
