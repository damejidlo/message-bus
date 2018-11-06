<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface IBusMessage
{

	/**
	 * Can be implemented by using
	 * @see PrivatePropertiesToArrayOfScalarsTrait
	 *
	 * @return mixed[] array of scalar values
	 */
	public function toArray() : array;

}
