<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Events;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\IEventSubscriber;
use Damejidlo\MessageBus\StaticAnalysis\Events\EventTypeExtractor;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class EventTypeExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$extractor = new EventTypeExtractor();

		Assert::same(SomeEvent::class, $extractor->extract(SomeSubscriber::class));
	}

}



class SomeEvent implements IEvent
{

}



class SomeSubscriber implements IEventSubscriber
{

	/**
	 * @param SomeEvent $event
	 */
	public function handle(SomeEvent $event) : void
	{
	}

}


(new EventTypeExtractorTest())->run();
