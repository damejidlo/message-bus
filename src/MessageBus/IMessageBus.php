<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus;

interface IMessageBus
{

	/**
	 * @param IMessage $message
	 * @return mixed
	 */
	public function handle(IMessage $message);

}
