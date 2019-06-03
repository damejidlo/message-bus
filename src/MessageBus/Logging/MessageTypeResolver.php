<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IMessage;



class MessageTypeResolver
{

	public function getMessageType(IMessage $message) : string
	{
		if ($message instanceof SubscriberSpecificDomainEvent) {
			return get_class($message->getEvent());
		}

		return get_class($message);
	}



	public function getSimplifiedMessageType(IMessage $message) : string
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
