<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Logging\LogMessageResolver;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use Psr\Log\LoggerInterface;



class LoggingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var LogMessageResolver
	 */
	private $logMessageResolver;

	/**
	 * @var MessageContextResolver
	 */
	private $messageContextResolver;



	public function __construct(
		LoggerInterface $logger,
		?LogMessageResolver $logMessageResolver = NULL,
		?MessageContextResolver $messageContextResolver = NULL
	) {
		$this->logger = $logger;
		$this->logMessageResolver = $logMessageResolver ?? new LogMessageResolver();
		$this->messageContextResolver = $messageContextResolver ?? new MessageContextResolver();
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$messageContext = $this->messageContextResolver->getContext($message, $context);

		$this->logger->info(
			$this->logMessageResolver->getHandlingStartedMessage($message, $context),
			$messageContext
		);

		try {
			$result = $nextMiddlewareCallback($message, $context);
			$this->logger->info(
				$this->logMessageResolver->getHandlingEndedSuccessfullyMessage($message, $context),
				$messageContext
			);

			return $result;

		} catch (\Throwable $exception) {
			$logMessage = $this->logMessageResolver->getHandlingEndedWithErrorMessage($message, $context, $exception);

			$messageContext['exceptionType'] = get_class($exception);
			$messageContext['exceptionMessage'] = $exception->getMessage();

			$this->logger->warning($logMessage, $messageContext);

			throw $exception;
		}
	}

}
