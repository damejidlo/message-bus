<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Middleware;

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventSubscribersResolver;
use Damejidlo\EventBus\ISubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Nette\SmartObject;



final class SubscribersResolvingMiddleware implements IMessageBusMiddleware
{

	use SmartObject;

	/**
	 * @var IEventSubscribersResolver
	 */
	private $eventSubscribersResolver;

	/**
	 * @var ISubscriberSpecificDomainEventHandler
	 */
	private $subscriberSpecificDomainEventHandler;



	public function __construct(
		IEventSubscribersResolver $eventSubscribersResolver,
		ISubscriberSpecificDomainEventHandler $subscriberSpecificDomainEventHandler
	) {
		$this->eventSubscribersResolver = $eventSubscribersResolver;
		$this->subscriberSpecificDomainEventHandler = $subscriberSpecificDomainEventHandler;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$event = $this->castMessageToEvent($message);

		$subscriberTypes = $this->eventSubscribersResolver->resolve($event);

		foreach ($subscriberTypes as $subscriberType) {
			$subscriberSpecificDomainEvent = new SubscriberSpecificDomainEvent($event, $subscriberType);
			$this->subscriberSpecificDomainEventHandler->handle($subscriberSpecificDomainEvent);
		}
	}



	private function castMessageToEvent(IBusMessage $message) : IDomainEvent
	{
		if (!$message instanceof IDomainEvent) {
			throw new \InvalidArgumentException(sprintf('IDomainEvent instance expected, %s given.', get_class($message)));
		}

		return $message;
	}

}
