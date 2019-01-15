<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\ILoggableBusMessage;



final class SubscriberSpecificDomainEvent implements IBusMessage, ILoggableBusMessage
{

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
	public function getLoggingContext() : array
	{
		$result = [
			'subscriberType' => $this->subscriberType,
			'eventType' => get_class($this->event),
		];

		if ($this->event instanceof ILoggableBusMessage) {
			$result = array_merge($result, $this->event->getLoggingContext());
		}

		return $result;
	}

}
