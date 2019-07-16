<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

use Damejidlo\CommandBus\ICommandHandler;
use Damejidlo\CommandBus\NewEntityId;
use Damejidlo\EventBus\IEventDispatcher;



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
