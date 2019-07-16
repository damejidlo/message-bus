<?php
declare(strict_types = 1);

namespace Damejidlo\Events;

class InMemoryEventQueue
{

	/**
	 * @var IDomainEvent[]
	 */
	private $queue = [];



	public function enqueue(IDomainEvent $event) : void
	{
		$this->queue[] = $event;
	}



	/**
	 * @return IDomainEvent[]
	 */
	public function releaseEvents() : array
	{
		$queue = $this->queue;
		$this->queue = [];

		return $queue;
	}

}