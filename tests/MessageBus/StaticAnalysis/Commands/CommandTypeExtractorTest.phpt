<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\StaticAnalysis\Commands\CommandTypeExtractor;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidCommand;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidHandler;
use Tester\Assert;



class CommandTypeExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$extractor = new CommandTypeExtractor();

		Assert::same(ValidCommand::class, $extractor->extract(ValidHandler::class));
	}

}


(new CommandTypeExtractorTest())->run();
