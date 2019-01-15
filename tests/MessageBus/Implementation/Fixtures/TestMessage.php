<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Implementation\Fixtures;

use Damejidlo\MessageBus\IBusMessage;



final class TestMessage implements IBusMessage
{

	/**
	 * @var \DateTimeImmutable
	 */
	public $datetime;



	public function __construct(\DateTimeImmutable $datetime)
	{
		$this->datetime = $datetime;
	}



	/**
	 * @inheritdoc
	 */
	public function getLoggingContext() : array
	{
		return [];
	}

}
