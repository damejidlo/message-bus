<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IBusMessage;
use Nette\SmartObject;



final class SubscriberSpecificDomainEvent implements IBusMessage
{

	use SmartObject;

	/**
	 * @var IDomainEvent
	 */
	private $event;

	/**
	 * @var string
	 */
	private $subscriberType;



	public function __construct(IDomainEvent $event, string $subscriberType)
	{
		$this->event = $event;
		$this->subscriberType = $subscriberType;
	}



	public function getEvent() : IDomainEvent
	{
		return $this->event;
	}



	public function getSubscriberType() : string
	{
		return $this->subscriberType;
	}



	/**
	 * @return mixed[]
	 */
	public function toArray() : array
	{
		$data = [
			'subscriberType' => $this->subscriberType,
			'eventType' => get_class($this->event),
		];

		return array_merge($data, $this->event->toArray());
	}

}
