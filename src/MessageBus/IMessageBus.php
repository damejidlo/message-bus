<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus;

use Damejidlo\MessageBus\Middleware\MiddlewareContext;



interface IMessageBus
{

	/**
	 * @param IMessage $message
	 * @param MiddlewareContext $context
	 * @return mixed
	 */
	public function handle(IMessage $message, MiddlewareContext $context);

}
