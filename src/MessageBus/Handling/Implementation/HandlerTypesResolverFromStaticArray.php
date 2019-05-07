<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling\Implementation;

use Damejidlo\MessageBus\Handling\HandlerTypes;
use Damejidlo\MessageBus\Handling\IHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\MessageType;



final class HandlerTypesResolverFromStaticArray implements IHandlerTypesResolver
{

	/**
	 * @var string[][]
	 */
	private $handlerTypesByMessageType;



	/**
	 * @param string[][] $handlerTypesByMessageType
	 */
	public function __construct(array $handlerTypesByMessageType)
	{
		$this->handlerTypesByMessageType = $handlerTypesByMessageType;
	}



	public function resolve(MessageType $messageType) : HandlerTypes
	{
		$messageTypeAsString = $messageType->toString();

		if (! isset($this->handlerTypesByMessageType[$messageTypeAsString])) {
			throw new \OutOfRangeException(sprintf('Handler types for message of type "%s" could not be found.', $messageTypeAsString));
		}

		return HandlerTypes::fromArrayOfStrings(...$this->handlerTypesByMessageType[$messageTypeAsString]);
	}

}
