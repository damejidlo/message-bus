<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

interface IEventSubscriberProvider
{

	/**
	 * @param string $subscriberType
	 * @return IEventSubscriber
	 *
	 * @throws EventSubscriberNotFoundException
	 */
	public function getByType(string $subscriberType) : IEventSubscriber;

}
