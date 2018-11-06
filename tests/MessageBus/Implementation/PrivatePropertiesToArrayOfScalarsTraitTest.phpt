<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Implementation;

require_once __DIR__ . '/../../../bootstrap.php';

use Consistence\Enum\Enum;
use Damejidlo\Mail\EmailAddress;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Implementation\PrivatePropertiesToArrayOfScalarsTrait;
use Damejidlo\Sms\PhoneNumber;
use DamejidloTests\DjTestCase;
use Money\Currency;
use Money\Money;
use Tester\Assert;



class PrivatePropertiesToArrayOfScalarsTraitTest extends DjTestCase
{

	public function testWhatava() : void
	{
		$object = new Message();

		Assert::equal([
			'public' => 'public',
			'protected' => 'protected',
			'private' => 'private',
			'string' => 'string',
			'integer' => 42,
			'float' => 66.6,
			'bool' => TRUE,
			'array' => [
				'integer' => 1,
				'string' => 'item',
				'array' => [
					'integer' => 1,
					'string' => 'item',
				],
				'dateTime' => '2018-01-01 00:00:00',
				'money' => '10',
				'emailAddress' => 'someone@damejidlo.cz',
				'phoneNumber' => '+420123456789',
			],
			'enum' => 'enum_item',
			'dto' => 'foo',
		], $object->toArray());
	}

}



class Message implements IBusMessage
{

	use PrivatePropertiesToArrayOfScalarsTrait;

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
	 * @var TestEnum
	 */
	private $enum;

	/**
	 * @var TestDTO
	 */
	private $dto;



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
			'dateTime' => new \DateTime('2018-01-01 0:00:00'),
			'money' => new Money(10, new Currency('CZK')),
			'emailAddress' => new EmailAddress('someone@damejidlo.cz'),
			'phoneNumber' => new PhoneNumber('+420123456789'),
		];

		$this->object = new \stdClass();

		$this->enum = TestEnum::get(TestEnum::ITEM);

		$this->dto = new TestDTO();
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
		$this->enum;
		$this->dto;
	}

}



class TestEnum extends Enum
{

	public const ITEM = 'enum_item';

}


class TestDTO {

	public function __toString() : string
	{
		return 'foo';
	}

}


(new PrivatePropertiesToArrayOfScalarsTraitTest())->run();
