<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;



class MessageTypeResolver
{

	public function getMessageType(IBusMessage $message) : string
	{
		if ($message instanceof ICommand) {
			return 'command';

		} elseif ($message instanceof IDomainEvent) {
			return 'event';

		} elseif ($message instanceof SubscriberSpecificDomainEvent) {
			return 'event';

		} else {
			return 'message';
		}
	}

}
