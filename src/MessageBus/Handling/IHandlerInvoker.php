<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageHandler;



interface IHandlerInvoker
{

	/**
	 * @param IMessageHandler $handler
	 * @param IBusMessage $message
	 * @return mixed
	 */
	public function invoke(IMessageHandler $handler, IBusMessage $message);

}
