<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Implementation;

use Damejidlo\CommandBus\CommandHandlerNotFoundException;
use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\ICommandHandlerResolver;
use Nette\SmartObject;



final class CommandHandlerResolver implements ICommandHandlerResolver
{

	use SmartObject;

	/**
	 * @var string[]
	 */
	private $handlerTypesByCommandType = [];



	/**
	 * @param string $commandType
	 * @param string $handlerType
	 */
	public function registerHandler(string $commandType, string $handlerType) : void
	{
		$this->handlerTypesByCommandType[$commandType] = $handlerType;
	}



	/**
	 * @inheritdoc
	 */
	public function resolve(ICommand $command) : string
	{
		$commandType = get_class($command);

		if (!array_key_exists($commandType, $this->handlerTypesByCommandType)) {
			throw new CommandHandlerNotFoundException(sprintf('Command handler for command "%s" not registered.', $commandType));
		}

		return $this->handlerTypesByCommandType[$commandType];
	}

}
