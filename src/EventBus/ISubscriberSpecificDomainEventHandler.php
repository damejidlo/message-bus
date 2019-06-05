<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IMessageHandler;



interface ISubscriberSpecificDomainEventHandler extends IMessageHandler
{

	public function handle(SubscriberSpecificDomainEvent $message) : void;

}
