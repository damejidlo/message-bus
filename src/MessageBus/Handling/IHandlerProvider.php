<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessageHandler;



interface IHandlerProvider
{

	public function get(HandlerType $type) : IMessageHandler;

}
