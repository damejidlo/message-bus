<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;



class LogMessageResolver
{

	/**
	 * @var MessageTypeResolver
	 */
	private $messageTypeResolver;



	public function __construct(
		?MessageTypeResolver $messageTypeResolver = NULL
	) {
		$this->messageTypeResolver = $messageTypeResolver ?? new MessageTypeResolver();
	}



	public function getHandlingStartedMessage(IBusMessage $message) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s started.',
				$this->messageTypeResolver->getMessageType($message),
				$this->getWhere($message)
			)
		);
	}



	public function getHandlingEndedSuccessfullyMessage(IBusMessage $message) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s ended successfully.',
				$this->messageTypeResolver->getMessageType($message),
				$this->getWhere($message)
			)
		);
	}



	public function getHandlingEndedWithErrorMessage(IBusMessage $message, \Throwable $exception) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s ended with error: %s',
				$this->messageTypeResolver->getMessageType($message),
				$this->getWhere($message),
				$exception->getMessage()
			)
		);
	}



	private function getWhere(IBusMessage $message) : string
	{
		if ($message instanceof SubscriberSpecificDomainEvent) {
			return ' in subscriber';
		}

		return '';
	}

}
