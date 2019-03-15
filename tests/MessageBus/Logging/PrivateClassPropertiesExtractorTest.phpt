<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Logging\PrivateClassPropertiesExtractor;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestMessageWithPrivateProperties;
use Tester\Assert;



class PrivateClassPropertiesExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$object = new TestMessageWithPrivateProperties();
		$extractedProperties = (new PrivateClassPropertiesExtractor())->extract($object);

		Assert::equal([
			'privateProperty' => 'foo',
		], $extractedProperties);
	}

}







(new PrivateClassPropertiesExtractorTest())->run();
