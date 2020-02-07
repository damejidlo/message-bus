<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Commands\NewEntityId;



final class HandleMethodHasNullableNewEntityIdReturnTypeHandler implements ICommandHandler
{

	public function handle(ValidCommand $command) : ?NewEntityId
	{
		return NULL;
	}

}
