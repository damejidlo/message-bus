<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

interface ILoggableBusMessage
{

	/**
	 * @return mixed[]
	 */
	public function getLoggingContext() : array;

}
