<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

interface IHandlerTypesResolver
{

	public function resolve(MessageType $messageType) : HandlerTypes;

}
