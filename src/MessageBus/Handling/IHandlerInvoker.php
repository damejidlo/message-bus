<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageHandler;



interface IHandlerInvoker
{

	/**
	 * @param IMessageHandler $handler
	 * @param IMessage $message
	 * @return mixed
	 */
	public function invoke(IMessageHandler $handler, IMessage $message);

}
