<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Fakes;

use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class RecordingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var IMessage|NULL
	 */
	private $message = NULL;

	/**
	 * @var MiddlewareContext|NULL
	 */
	private $context = NULL;

	/**
	 * @var MiddlewareLog
	 */
	private $log;



	public function __construct(int $id, MiddlewareLog $log)
	{
		$this->id = $id;
		$this->log = $log;
	}



	/**
	 * @inheritDoc
	 */
	public function handle(IMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$this->message = $message;
		$this->context = $context;

		$this->log->middlewareCalled[$this->id] = $this;

		return $nextMiddlewareCallback($message, $context);
	}



	public function getMessage() : IMessage
	{
		if ($this->message === NULL) {
			throw new \LogicException('Message not set.');
		}

		return $this->message;
	}



	public function getContext() : MiddlewareContext
	{
		if ($this->context === NULL) {
			throw new \LogicException('Context not set.');
		}

		return $this->context;
	}

}
