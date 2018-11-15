<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Middleware;

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Implementation\MessageHashCalculator;
use Psr\Log\LoggerInterface;



class SubscriberSpecificLoggingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var MessageHashCalculator
	 */
	private $messageHashCalculator;



	public function __construct(LoggerInterface $logger, MessageHashCalculator $messageHashCalculator)
	{
		$this->logger = $logger;
		$this->messageHashCalculator = $messageHashCalculator;
	}



	/**
	 * @param IBusMessage|SubscriberSpecificDomainEvent $message
	 * @param \Closure $nextMiddlewareCallback
	 * @return mixed
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$subscriberSpecificDomainEvent = $this->castMessageToSubscriberSpecificDomainEvent($message);

		$context = $subscriberSpecificDomainEvent->toArray();
		$context['eventHash'] = $this->messageHashCalculator->calculateHash($subscriberSpecificDomainEvent->getEvent());

		$this->logger->info('Event handling in subscriber started.', $context);

		try {
			$result = $nextMiddlewareCallback($subscriberSpecificDomainEvent);
			$this->logger->info('Event handling in subscriber ended successfully.', $context);

			return $result;

		} catch (\Throwable $exception) {
			$subscriberSpecificDomainEvent = sprintf('Event handling in subscriber ended with error: %s', $exception->getMessage());

			$context['exceptionType'] = get_class($exception);
			$context['exceptionMessage'] = $exception->getMessage();

			$this->logger->error($subscriberSpecificDomainEvent, $context);

			throw $exception;
		}
	}



	private function castMessageToSubscriberSpecificDomainEvent(IBusMessage $message) : SubscriberSpecificDomainEvent
	{
		if (!$message instanceof SubscriberSpecificDomainEvent) {
			throw new \InvalidArgumentException(sprintf('SubscriberSpecificDomainEvent instance expected, %s given.', get_class($message)));
		}

		return $message;
	}

}
