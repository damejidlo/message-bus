<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\Events\IEventSubscriber;



final class NotifyUserOnOrderPlaced implements IEventSubscriber
{

	/**
	 * @var bool
	 */
	private $invoked = FALSE;



	public function handle(OrderPlacedEvent $event) : void
	{
		$this->invoked = TRUE;
	}



	public function wasInvoked() : bool
	{
		return $this->invoked;
	}

}
