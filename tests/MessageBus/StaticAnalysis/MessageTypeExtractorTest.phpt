<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\StaticAnalysis\MessageTypeExtractor;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidCommand;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidHandler;
use Tester\Assert;



class MessageTypeExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$extractor = new MessageTypeExtractor();

		Assert::same(ValidCommand::class, $extractor->extract(HandlerType::fromString(ValidHandler::class), 'handle')->toString());
	}

}


(new MessageTypeExtractorTest())->run();
