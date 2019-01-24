<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use Damejidlo\MessageBus\Logging\MessageTypeResolver;
use Psr\Log\LoggerInterface;



class LoggingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var MessageTypeResolver
	 */
	private $messageTypeResolver;

	/**
	 * @var MessageContextResolver
	 */
	private $messageContextResolver;



	public function __construct(
		LoggerInterface $logger,
		?MessageTypeResolver $messageTypeResolver = NULL,
		?MessageContextResolver $messageContextResolver = NULL
	) {
		$this->logger = $logger;
		$this->messageTypeResolver = $messageTypeResolver ?? new MessageTypeResolver();
		$this->messageContextResolver = $messageContextResolver ?? new MessageContextResolver();
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$messageType = $this->messageTypeResolver->getMessageType($message);
		$messageTypeFirstUpper = ucfirst($messageType);

		$context = $this->messageContextResolver->getContext($message);

		$this->logger->info(
			sprintf('%s handling started.', $messageTypeFirstUpper),
			$context
		);

		try {
			$result = $nextMiddlewareCallback($message);
			$this->logger->info(
				sprintf('%s handling ended successfully.', $messageTypeFirstUpper),
				$context
			);

			return $result;

		} catch (\Throwable $exception) {
			$logMessage = sprintf('%s handling ended with error: %s', $messageTypeFirstUpper, $exception->getMessage());

			$context['exceptionType'] = get_class($exception);
			$context['exceptionMessage'] = $exception->getMessage();

			$this->logger->warning($logMessage, $context);

			throw $exception;
		}
	}

}
