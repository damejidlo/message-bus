<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fakes;

use Damejidlo\CommandBus\ICommandHandler;
use Damejidlo\CommandBus\ICommandHandlerProvider;



final class FakeCommandHandlerProvider implements ICommandHandlerProvider
{

	/**
	 * @var ICommandHandler[]
	 */
	private $handlersByType;



	/**
	 * @param ICommandHandler[] $handlers
	 */
	public function __construct(array $handlers)
	{
		foreach ($handlers as $handler) {
			$this->handlersByType[get_class($handler)] = $handler;
		}
	}



	/**
	 * @inheritDoc
	 */
	public function getByType(string $handlerType) : ICommandHandler
	{
		return $this->handlersByType[$handlerType];
	}

}
