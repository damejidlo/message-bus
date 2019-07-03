<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

use Damejidlo\MessageBus\IMessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class EventBus implements IEventBus
{

	/**
	 * @var IMessageBus
	 */
	private $delegate;



	public function __construct(IMessageBus $delegate)
	{
		$this->delegate = $delegate;
	}



	public function handle(IDomainEvent $event) : void
	{
		$this->delegate->handle($event, MiddlewareContext::empty());
	}

}
