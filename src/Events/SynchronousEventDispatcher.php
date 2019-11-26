<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Events;

use Damejidlo\MessageBus\IMessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class SynchronousEventDispatcher implements IEventDispatcher
{

	/**
	 * @var IMessageBus
	 */
	private $messageBus;



	public function __construct(IMessageBus $messageBus)
	{
		$this->messageBus = $messageBus;
	}



	public function dispatch(IEvent $event) : void
	{
		$this->messageBus->handle($event, MiddlewareContext::empty());
	}

}
