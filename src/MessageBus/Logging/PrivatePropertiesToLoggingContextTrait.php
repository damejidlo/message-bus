<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

trait PrivatePropertiesToLoggingContextTrait
{

	/**
	 * Implements
	 * @see ILoggableBusMessage::getLoggingContext()
	 *
	 * @return mixed[] array of scalar values
	 */
	public function getLoggingContext() : array
	{
		$array = get_object_vars($this);

		$array = $this->toArrayOfScalarsRecursive($array);

		return $array;
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
