<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

final class HandlerCannotBeProvidedException extends \RuntimeException
{

	public static function fromHandlerType(HandlerType $handlerType) : self
	{
		$exceptionMessage = sprintf('Handler of type "%s" cannot be provided.', $handlerType->toString());

		return new self($exceptionMessage);
	}

}
