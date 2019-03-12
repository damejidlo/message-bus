<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

use Damejidlo\MessageBus\IBusMessage;



class TestMessageWithPrivateProperties implements IBusMessage
{

	/**
	 * @var string
	 */
	private $privateProperty = 'foo';



	protected function satisfyPhpStan() : void
	{
		$this->privateProperty;
	}

}
