<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestLoggableMessageWithDifferentProperties;
use Tester\Assert;



class PrivatePropertiesToLoggingContextTraitTest extends DjTestCase
{

	public function testTrait() : void
	{
		$object = new TestLoggableMessageWithDifferentProperties();

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
			],
			'dtoWithToStringMethod' => 'toString',
			'dtoWithMagicToStringMethod' => 'magicToString',
		], $object->getLoggingContext());
	}

}







(new PrivatePropertiesToLoggingContextTraitTest())->run();
