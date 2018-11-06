<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IBusMessageHandler;



interface ISubscriberSpecificDomainEventHandler extends IBusMessageHandler
{

	public function handle(SubscriberSpecificDomainEvent $message) : void;

}
