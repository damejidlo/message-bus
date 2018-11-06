<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Implementation;

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventSubscribersResolver;
use Nette\SmartObject;



final class EventSubscribersResolver implements IEventSubscribersResolver
{

	use SmartObject;

	/**
	 * @var string[][]
	 */
	private $subscriberTypesByEventType = [];



	public function registerSubscriber(string $eventType, string $subscriberType) : void
	{
		$this->subscriberTypesByEventType[$eventType][] = $subscriberType;
	}



	/**
	 * @inheritdoc
	 */
	public function resolve(IDomainEvent $event) : array
	{
		$eventType = get_class($event);

		return $this->subscriberTypesByEventType[$eventType] ?? [];
	}

}
