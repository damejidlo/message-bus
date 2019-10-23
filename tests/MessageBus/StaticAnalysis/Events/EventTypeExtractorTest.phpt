<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Events;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\StaticAnalysis\Events\EventTypeExtractor;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\DoSomethingOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\SomethingValidHappenedEvent;
use Tester\Assert;



class EventTypeExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$extractor = new EventTypeExtractor();

		Assert::same(SomethingValidHappenedEvent::class, $extractor->extract(DoSomethingOnSomethingValidHappened::class));
	}

}


(new EventTypeExtractorTest())->run();
