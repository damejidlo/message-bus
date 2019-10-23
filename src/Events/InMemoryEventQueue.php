<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Events;

class InMemoryEventQueue
{

	/**
	 * @var IEvent[]
	 */
	private $queue = [];



	public function enqueue(IEvent $event) : void
	{
		$this->queue[] = $event;
	}



	/**
	 * @return IEvent[]
	 */
	public function releaseEvents() : array
	{
		$queue = $this->queue;
		$this->queue = [];

		return $queue;
	}

}
