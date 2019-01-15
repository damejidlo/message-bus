<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface IBusMessage
{

	/**
	 * @return mixed[] array of scalar values
	 */
	public function toArray() : array;

}
