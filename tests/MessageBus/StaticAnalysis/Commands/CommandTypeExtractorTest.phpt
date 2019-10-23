<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\StaticAnalysis\Commands\CommandTypeExtractor;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class CommandTypeExtractorTest extends DjTestCase
{

	public function testExtract() : void
	{
		$extractor = new CommandTypeExtractor();

		Assert::same(SomeCommand::class, $extractor->extract(SomeHandler::class));
	}

}



class SomeCommand implements ICommand
{

}



class SomeHandler implements ICommandHandler
{

	/**
	 * @param SomeCommand $command
	 */
	public function handle(SomeCommand $command) : void
	{
	}

}


(new CommandTypeExtractorTest())->run();
