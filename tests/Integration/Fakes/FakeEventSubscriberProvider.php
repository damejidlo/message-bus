<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fakes;

use Damejidlo\EventBus\IEventSubscriber;
use Damejidlo\EventBus\IEventSubscriberProvider;



final class FakeEventSubscriberProvider implements IEventSubscriberProvider
{

	/**
	 * @var IEventSubscriber[]
	 */
	private $subscribersByType;



	/**
	 * @param IEventSubscriber[] $subscribers
	 */
	public function __construct(array $subscribers)
	{
		foreach ($subscribers as $subscriber) {
			$this->subscribersByType[get_class($subscriber)] = $subscriber;
		}
	}



	public function getByType(string $subscriberType) : IEventSubscriber
	{
		return $this->subscribersByType[$subscriberType];
	}

}
