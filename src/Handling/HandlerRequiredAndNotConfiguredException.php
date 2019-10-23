<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

final class HandlerRequiredAndNotConfiguredException extends \RuntimeException
{

	public static function fromMessageType(MessageType $messageType) : self
	{
		$exceptionMessage = sprintf('Handler required and not configured for message of type "%s".', $messageType->toString());

		return new self($exceptionMessage);
	}

}
