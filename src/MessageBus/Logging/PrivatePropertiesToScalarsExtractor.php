<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

class PrivatePropertiesToScalarsExtractor
{

	/**
	 * Implements
	 * @see ILoggableBusMessage::getLoggingContext()
	 *
	 * @param object $object
	 * @return mixed[] array of scalar values
	 */
	public function extract($object) : array
	{
		$array = $this->extractVariables($object);

		$array = $this->toArrayOfScalarsRecursive($array);

		return $array;
	}



	/**
	 * @param object $object
	 * @return mixed[]
	 */
	private function extractVariables($object) : array
	{
		// magic :)
		$extract = \Closure::bind(
			function ($object) {
				return get_object_vars($object);
			},
			NULL,
			$object
		);

		return $extract($object);
	}



	/**
	 * @internal
	 *
	 * @param mixed[] $array
	 * @return mixed[]
	 */
	private function toArrayOfScalarsRecursive(array $array) : array
	{
		$array = $this->formatProperties($array);
		$array = $this->filterOutNonScalarAndNotArray($array);

		array_walk($array, function (&$value) : void {
			if (is_array($value)) {
				$value = $this->toArrayOfScalarsRecursive($value);
			}
		});

		return $array;
	}



	/**
	 * @internal
	 *
	 * @param mixed[] $array
	 * @return mixed[]
	 */
	private function formatProperties(array $array) : array
	{
		return array_map(function ($value) {
			if ($value instanceof \DateTimeInterface) {
				return $value->format('Y-m-d H:i:s');
			}

			if (is_object($value) && method_exists($value, 'toString')) {
				return $value->toString();
			}

			if (is_object($value) && method_exists($value, '__toString')) {
				return $value->__toString();
			}

			return $value;
		}, $array);
	}



	/**
	 * @internal
	 *
	 * @param mixed[] $array
	 * @return mixed[]
	 */
	private function filterOutNonScalarAndNotArray(array $array) : array
	{
		return array_filter($array, function ($value) : bool {
			return is_scalar($value) || is_array($value);
		});
	}

}
