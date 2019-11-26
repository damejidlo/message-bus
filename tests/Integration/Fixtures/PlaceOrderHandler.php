<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\Events\IEventDispatcher;



class PlaceOrderHandler implements ICommandHandler
{

	/**
	 * @var IEventDispatcher
	 */
	private $eventDispatcher;



	public function __construct(IEventDispatcher $dispatcher)
	{
		$this->eventDispatcher = $dispatcher;
	}



	public function handle(PlaceOrderCommand $command) : NewEntityId
	{
		$event = new OrderPlacedEvent();
		$this->eventDispatcher->dispatch($event);

		return NewEntityId::fromInteger(1);
	}

}
