<?php
declare(strict_types = 1);

namespace Damejidlo\Commands;

interface ICommandBus
{

	public function handle(ICommand $command) : ?NewEntityId;

}
