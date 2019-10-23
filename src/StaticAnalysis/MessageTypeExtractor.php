<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

class MessageTypeExtractor
{

	public function extract(string $handlerServiceClass) : string
	{
		$reflection = new \ReflectionClass($handlerServiceClass);
		$handleMethod = $reflection->getMethod('handle');

		$handleMethodParameters = $handleMethod->getParameters();
		$handleMethodParameter = $handleMethodParameters[0];

		$parameterType = $handleMethodParameter->getType();
		if ($parameterType === NULL) {
			throw new \LogicException(
				sprintf('Handle method parameter type of class "%s" must be defined in this context.', $handlerServiceClass)
			);
		}

		return $parameterType->getName();
	}

}
