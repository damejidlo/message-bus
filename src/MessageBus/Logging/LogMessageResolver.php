<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\MessageType;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class LogMessageResolver
{

	public function getHandlingStartedMessage(IMessage $message, MiddlewareContext $context) : string
	{
		return sprintf(
			'%s handling%s started.',
			MessageType::fromMessage($message)->toGeneralType(),
			$this->getWhere($message, $context)
		);
	}



	public function getHandlingEndedSuccessfullyMessage(IMessage $message, MiddlewareContext $context) : string
	{
		return sprintf(
			'%s handling%s ended successfully.',
			MessageType::fromMessage($message)->toGeneralType(),
			$this->getWhere($message, $context)
		);
	}



	public function getHandlingEndedWithErrorMessage(IMessage $message, MiddlewareContext $context, \Throwable $exception) : string
	{
		return sprintf(
			'%s handling%s ended with error: %s',
			MessageType::fromMessage($message)->toGeneralType(),
			$this->getWhere($message, $context),
			$exception->getMessage()
		);
	}



	private function getWhere(IMessage $message, MiddlewareContext $context) : string
	{
		$handlerIsResolved = $context->has(HandlerType::CONTEXT_KEY);

		if ($handlerIsResolved && $message instanceof IDomainEvent) {
			return ' in subscriber';
		}

		return '';
	}

}
