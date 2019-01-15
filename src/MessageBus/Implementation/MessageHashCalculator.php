<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Implementation;

use Damejidlo\MessageBus\IBusMessage;



class MessageHashCalculator
{

	public function calculateHash(IBusMessage $message) : string
	{
		$data = $message->getLoggingContext();
		$serializedMessage = sprintf('%s:%s', get_class($message), serialize($data));

		return sha1($serializedMessage);
	}

}
