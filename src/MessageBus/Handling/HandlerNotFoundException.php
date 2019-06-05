<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

class HandlerNotFoundException extends \RuntimeException
{

	public static function fromMessageType(MessageType $messageType) : self
	{
		$exceptionMessage = sprintf('Handler types for message of type "%s" could not be found.', $messageType->toString());

		return new self($exceptionMessage);
	}

}
