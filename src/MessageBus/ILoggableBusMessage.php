<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface ILoggableBusMessage
{

	/**
	 * @return mixed[]
	 */
	public function getLoggingContext() : array;

}
