<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Commands;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Commands\NewEntityId;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class NewEntityIdTest extends DjTestCase
{

	public function testCreateFromInteger() : void
	{
		$id = 666;

		$newEntityId = NewEntityId::fromInteger($id);

		Assert::same($id, $newEntityId->toInteger());
		Assert::same((string) $id, $newEntityId->getValue());
	}



	public function testCreateFromIntegerString() : void
	{
		$id = '666';

		$newEntityId = new NewEntityId($id);

		Assert::same($id, $newEntityId->getValue());
		Assert::same((int) $id, $newEntityId->toInteger());
	}



	/**
	 * @dataProvider getDataForCreateFromNonIntegerString
	 *
	 * @param string $value
	 */
	public function testCreateFromNonIntegerString(string $value) : void
	{
		$newEntityId = new NewEntityId($value);

		Assert::same($value, $newEntityId->getValue());
		Assert::exception(function () use ($newEntityId) : void {
			$newEntityId->toInteger();
		}, \LogicException::class);
	}



	/**
	 * @return mixed[]
	 */
	protected function getDataForCreateFromNonIntegerString() : array
	{
		return [
			['123e4567-e89b-12d3-a456-426655440000'],
			['99.90'],
		];
	}

}



(new NewEntityIdTest())->run();
