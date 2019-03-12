<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging\Fixtures;

use Damejidlo\MessageBus\IBusMessage;



class TestMessageWithDifferentProperties implements IBusMessage
{

	/**
	 * @var string
	 */
	public $public = 'public';

	/**
	 * @var string
	 */
	protected $protected = 'protected';

	/**
	 * @var string
	 */
	private $private = 'private';

	/**
	 * @var string
	 */
	private $string = 'string';

	/**
	 * @var int
	 */
	private $integer = 42;

	/**
	 * @var float
	 */
	private $float = 66.6;

	/**
	 * @var bool
	 */
	private $bool = TRUE;

	/**
	 * @var mixed[]
	 */
	private $array = [];

	/**
	 * @var \stdClass
	 */
	private $object;

	/**
	 * @var TestDtoWithToStringMethod
	 */
	private $dtoWithToStringMethod;

	/**
	 * @var TestDtoWithMagicToStringMethod
	 */
	private $dtoWithMagicToStringMethod;



	public function __construct()
	{
		$this->array = [
			'integer' => 1,
			'string' => 'item',
			'object' => new \stdClass(),
			'array' => [
				'integer' => 1,
				'string' => 'item',
				'object' => new \stdClass(),
			],
			'dateTime' => new \DateTimeImmutable('2018-01-01 0:00:00'),
		];

		$this->object = new \stdClass();
		$this->dtoWithToStringMethod = new TestDtoWithToStringMethod('toString');
		$this->dtoWithMagicToStringMethod = new TestDtoWithMagicToStringMethod('magicToString');
	}



	protected function satisfyPhpStan() : void
	{
		$this->private;
		$this->string;
		$this->integer;
		$this->float;
		$this->bool;
		$this->array;
		$this->object;
		$this->dtoWithToStringMethod;
		$this->dtoWithMagicToStringMethod;
	}

}
