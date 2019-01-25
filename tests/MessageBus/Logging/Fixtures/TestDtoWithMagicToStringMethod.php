<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

class TestDtoWithMagicToStringMethod
{

	/**
	 * @var string
	 */
	private $toStringMethodResult;



	public function __construct(string $toStringMethodResult)
	{
		$this->toStringMethodResult = $toStringMethodResult;
	}



	public function __toString() : string
	{
		return $this->toStringMethodResult;
	}

}
