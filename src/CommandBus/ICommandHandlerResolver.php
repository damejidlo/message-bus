<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus;

interface ICommandHandlerResolver
{

	/**
	 * @param ICommand $command
	 * @return string
	 *
	 * @throws CommandHandlerNotFoundException
	 */
	public function resolve(ICommand $command) : string;

}
