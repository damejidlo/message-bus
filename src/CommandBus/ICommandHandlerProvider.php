<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus;

interface ICommandHandlerProvider
{

	/**
	 * @param string $handlerType
	 * @return ICommandHandler
	 *
	 * @throws CommandHandlerNotFoundException
	 */
	public function getByType(string $handlerType) : ICommandHandler;

}
