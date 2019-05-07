<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling\Implementation;

use Damejidlo\MessageBus\Handling\IHandlerInvoker;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageHandler;



final class HandlerInvoker implements IHandlerInvoker
{

	/**
	 * @inheritDoc
	 */
	public function invoke(IMessageHandler $handler, IBusMessage $message)
	{
		$handleMethod = 'handle';
		$callback = [$handler, $handleMethod];

		if (!is_callable($callback)) {
			throw new \LogicException(
				sprintf('Method "%s" of handler "%s" is not callable.', $handleMethod, get_class($handler))
			);
		}

		return call_user_func($callback, $message);
	}

}
