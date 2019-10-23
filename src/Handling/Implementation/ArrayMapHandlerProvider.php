<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling\Implementation;

use Damejidlo\MessageBus\Handling\HandlerCannotBeProvidedException;
use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\IHandlerProvider;
use Damejidlo\MessageBus\IMessageHandler;



final class ArrayMapHandlerProvider implements IHandlerProvider
{

	/**
	 * @var IMessageHandler[]
	 */
	private $handlersByType;



	/**
	 * @param IMessageHandler[] $handlersByType
	 */
	public function __construct(array $handlersByType)
	{
		$this->handlersByType = $handlersByType;
	}



	public function get(HandlerType $type) : IMessageHandler
	{
		$typeAsString = $type->toString();

		if (! isset($this->handlersByType[$typeAsString])) {
			throw HandlerCannotBeProvidedException::fromHandlerType($type);
		}

		return $this->handlersByType[$typeAsString];
	}

}
