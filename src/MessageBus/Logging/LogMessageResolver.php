<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IMessage;



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



	public function getHandlingStartedMessage(IMessage $message) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s started.',
				$this->messageTypeResolver->getSimplifiedMessageType($message),
				$this->getWhere($message)
			)
		);
	}



	public function getHandlingEndedSuccessfullyMessage(IMessage $message) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s ended successfully.',
				$this->messageTypeResolver->getSimplifiedMessageType($message),
				$this->getWhere($message)
			)
		);
	}



	public function getHandlingEndedWithErrorMessage(IMessage $message, \Throwable $exception) : string
	{
		return ucfirst(
			sprintf(
				'%s handling%s ended with error: %s',
				$this->messageTypeResolver->getSimplifiedMessageType($message),
				$this->getWhere($message),
				$exception->getMessage()
			)
		);
	}



	private function getWhere(IMessage $message) : string
	{
		if ($message instanceof SubscriberSpecificDomainEvent) {
			return ' in subscriber';
		}

		return '';
	}

}
