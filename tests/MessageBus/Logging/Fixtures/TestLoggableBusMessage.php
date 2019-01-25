<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Logging\ILoggableBusMessage;



class TestLoggableBusMessage implements IBusMessage, ILoggableBusMessage
{

	/**
	 * @var mixed[]
	 */
	private $loggingContextToReturn;



	/**
	 * @param mixed[] $loggingContextToReturn
	 */
	public function __construct(array $loggingContextToReturn)
	{
		$this->loggingContextToReturn = $loggingContextToReturn;
	}



	/**
	 * @return mixed[]
	 */
	public function getLoggingContext() : array
	{
		return $this->loggingContextToReturn;
	}

}
