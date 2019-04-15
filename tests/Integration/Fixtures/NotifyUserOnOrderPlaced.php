<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\EventBus\IEventSubscriber;



final class NotifyUserOnOrderPlaced implements IEventSubscriber
{

	public function handle(OrderPlacedEvent $event) : void
	{
	}

}
