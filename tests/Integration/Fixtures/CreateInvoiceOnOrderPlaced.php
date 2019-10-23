<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class CreateInvoiceOnOrderPlaced implements IEventSubscriber
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
