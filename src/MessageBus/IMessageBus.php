<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface IMessageBus
{

	/**
	 * @param IBusMessage $message
	 * @return mixed
	 */
	public function handle(IBusMessage $message);

}
