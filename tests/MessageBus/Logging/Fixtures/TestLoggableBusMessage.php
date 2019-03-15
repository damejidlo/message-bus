<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

use Damejidlo\MessageBus\IBusMessage;



class TestLoggableBusMessage implements IBusMessage
{

	/**
	 * @param mixed[] $properties
	 */
	public function __construct(array $properties)
	{
		foreach ($properties as $key => $value) {
			$this->$key = $value;
		}
	}

}
