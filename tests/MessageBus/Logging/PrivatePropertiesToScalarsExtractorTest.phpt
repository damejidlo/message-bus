<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Logging\RecursiveArrayToScalarsTypecaster;
use Damejidlo\MessageBus\Logging\PrivateClassPropertiesExtractor;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestMessageWithDifferentProperties;
use Tester\Assert;



class PrivatePropertiesToScalarsExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$object = new TestMessageWithDifferentProperties();
		$extractedProperties = (new PrivateClassPropertiesExtractor())->extract($object);
		$castProperties = (new RecursiveArrayToScalarsTypecaster())->cast($extractedProperties);

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
		], $castProperties);
	}

}







(new PrivatePropertiesToScalarsExtractorTest())->run();
