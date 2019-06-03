<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;



/**
 * Do not allow nested handling of messages.
 */
class GuardAgainstNestedHandlingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var IsCurrentlyHandlingAwareMiddleware
	 */
	private $isCurrentlyHandlingAwareMiddleware;



	public function __construct(IsCurrentlyHandlingAwareMiddleware $isCurrentlyHandlingAwareMiddleware)
	{
		$this->isCurrentlyHandlingAwareMiddleware = $isCurrentlyHandlingAwareMiddleware;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		if ($this->isCurrentlyHandlingAwareMiddleware->isHandling()) {
			throw new AlreadyHandlingOtherMessageException('Already handling other message.');
		}

		return $nextMiddlewareCallback($message, $context);
	}

}
