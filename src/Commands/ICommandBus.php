<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Commands;

interface ICommandBus
{

	public function handle(ICommand $command) : ?NewEntityId;

}
