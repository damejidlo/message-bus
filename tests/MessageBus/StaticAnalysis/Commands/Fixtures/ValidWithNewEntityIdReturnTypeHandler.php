<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Commands\NewEntityId;



final class ValidWithNewEntityIdReturnTypeHandler implements ICommandHandler
{

	public function handle(ValidWithNewEntityIdReturnTypeCommand $command) : NewEntityId
	{
		return new NewEntityId('42');
	}

}
