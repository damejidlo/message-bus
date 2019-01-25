<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

class TestDtoWithToStringMethod
{

	/**
	 * @var string
	 */
	private $toStringMethodResult;



	public function __construct(string $toStringMethodResult)
	{
		$this->toStringMethodResult = $toStringMethodResult;
	}



	public function toString() : string
	{
		return $this->toStringMethodResult;
	}

}
