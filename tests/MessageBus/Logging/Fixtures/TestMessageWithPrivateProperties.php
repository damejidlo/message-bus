<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

use Damejidlo\MessageBus\IMessage;



class TestMessageWithPrivateProperties implements IMessage
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
