<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus;

interface ICommandBus
{

	public function handle(ICommand $command) : ?NewEntityId;

}
