<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus;

use Damejidlo\CommandBus\Implementation\NewEntityId;



interface ICommandBus
{

	public function handle(ICommand $command) : ?NewEntityId;

}
