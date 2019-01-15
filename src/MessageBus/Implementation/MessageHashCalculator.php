<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Implementation;

use Damejidlo\MessageBus\IBusMessage;



class MessageHashCalculator
{

	public function calculateHash(IBusMessage $message) : string
	{
		return sha1(serialize($message));
	}

}
