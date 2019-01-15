<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Implementation\MessageHashCalculator;
use Psr\Log\LoggerInterface;



class LoggingMiddleware implements IMessageBusMiddleware
{

	private const MESSAGE_ATTRIBUTE_KEY_PREFIX = 'messageAttribute_';

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
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$messageType = $this->getMessageType($message);
		$messageTypeFirstUpper = ucfirst($messageType);

		$context = [
			sprintf('%sType', $messageType) => get_class($message),
			sprintf('%sHash', $messageType) => $this->messageHashCalculator->calculateHash($message),
		];

		$messageAttributes = $this->getMessageAttributes($message);
		$context = array_merge($context, $messageAttributes);

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



	private function getMessageType(IBusMessage $message) : string
	{
		if ($message instanceof ICommand) {
			return 'command';

		} elseif ($message instanceof IDomainEvent) {
			return 'event';

		} else {
			return 'message';
		}
	}



	/**
	 * @param IBusMessage $message
	 * @return mixed[]
	 */
	private function getMessageAttributes(IBusMessage $message) : array
	{
		$attributes = $message->getLoggingContext();

		$keys = array_map(function (string $key) : string {
			return self::MESSAGE_ATTRIBUTE_KEY_PREFIX . $key;
		}, array_keys($attributes));

		$result = array_combine($keys, $attributes);

		if ($result === FALSE) {
			throw new \LogicException('Array combine failed.');
		}

		return $result;
	}

}
