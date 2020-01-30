<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\MessageType;



class MessageTypeExtractor
{

	public function extract(HandlerType $handlerType, string $handleMethodName) : MessageType
	{
		$reflection = new \ReflectionClass($handlerType->toString());
		$handleMethod = $reflection->getMethod($handleMethodName);

		$handleMethodParameters = $handleMethod->getParameters();
		$handleMethodParameter = $handleMethodParameters[0];

		$parameterType = $handleMethodParameter->getType();
		if ($parameterType === NULL) {
			throw new \LogicException(
				sprintf('Handle method parameter type of class "%s" must be defined in this context.', $handlerType->toString())
			);
		}

		return MessageType::fromString($parameterType->getName());
	}

}
