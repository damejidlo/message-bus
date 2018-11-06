<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus;

interface IEventSubscribersResolver
{

	/**
	 * @param IDomainEvent $event
	 * @return string[] array of subscriber types
	 */
	public function resolve(IDomainEvent $event) : array;

}
