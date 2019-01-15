<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Implementation\Fixtures;

use Damejidlo\MessageBus\IBusMessage;



final class TestMessageWithDifferentAttributes implements IBusMessage
{

	/**
	 * @var string
	 */
	public $string = 'some-string';



	/**
	 * @inheritdoc
	 */
	public function getLoggingContext() : array
	{
		return [];
	}

}
