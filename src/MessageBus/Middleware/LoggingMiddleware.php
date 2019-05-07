<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IBusMessage;
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
	public function handle(IBusMessage $message, MiddlewareCallback $nextMiddlewareCallback)
	{
		$context = $this->messageContextResolver->getContext($message);

		$this->logger->info(
			$this->logMessageResolver->getHandlingStartedMessage($message),
			$context
		);

		try {
			$result = $nextMiddlewareCallback($message);
			$this->logger->info(
				$this->logMessageResolver->getHandlingEndedSuccessfullyMessage($message),
				$context
			);

			return $result;

		} catch (\Throwable $exception) {
			$logMessage = $this->logMessageResolver->getHandlingEndedWithErrorMessage($message, $exception);

			$context['exceptionType'] = get_class($exception);
			$context['exceptionMessage'] = $exception->getMessage();

			$this->logger->warning($logMessage, $context);

			throw $exception;
		}
	}

}
