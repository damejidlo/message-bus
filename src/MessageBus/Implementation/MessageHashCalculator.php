<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Implementation;

use Damejidlo\MessageBus\IBusMessage;
use Nette\SmartObject;



class MessageHashCalculator
{

	use SmartObject;



	public function calculateHash(IBusMessage $message) : string
	{
		$data = $message->toArray();
		$serializedMessage = sprintf('%s:%s', get_class($message), serialize($data));

		return sha1($serializedMessage);
	}

}
