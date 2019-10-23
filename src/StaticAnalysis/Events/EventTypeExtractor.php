<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Events;

class EventTypeExtractor
{

	/**
	 * @param string $subscriberServiceClass
	 * @return string event type
	 */
	public function extract(string $subscriberServiceClass) : string
	{
		$reflection = new \ReflectionClass($subscriberServiceClass);
		$handleMethod = $reflection->getMethod('handle');

		$handleMethodParameters = $handleMethod->getParameters();
		$handleMethodParameter = $handleMethodParameters[0];

		$parameterType = $handleMethodParameter->getType();
		if ($parameterType === NULL) {
			throw new \LogicException(
				sprintf('Handle method parameter type of class "%s" must be defined in this context.', $subscriberServiceClass)
			);
		}

		return $parameterType->getName();
	}

}
