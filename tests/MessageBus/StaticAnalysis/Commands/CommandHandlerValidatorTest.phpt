<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\StaticAnalysis\Commands\CommandHandlerValidator;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\CommandHasIncorrectNameHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\CommandNameDoesNotMatchHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasIncorrectlyNamedParameterHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasIncorrectReturnTypeHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasMoreParametersHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNoParameterHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNoReturnTypeHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNullableNewEntityIdReturnTypeHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasParameterWithIncorrectTypeHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodNotPublicHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\MissingHandleMethodHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\NotFinalCommandHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\NotFinalHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidHandler;
use DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\ValidWithNewEntityIdReturnTypeHandler;
use Tester\Assert;



class CommandHandlerValidatorTest extends DjTestCase
{

	/**
	 * @dataProvider getDataForValidateSucceeds
	 *
	 * @param string $handlerClassName
	 */
	public function testValidateSucceeds(string $handlerClassName) : void
	{
		$validator = new CommandHandlerValidator();

		Assert::noError(function () use ($validator, $handlerClassName) : void {
			$validator->validate($handlerClassName);
		});
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateSucceeds() : array
	{
		return [
			[ValidHandler::class],
			[ValidWithNewEntityIdReturnTypeHandler::class],
		];
	}



	/**
	 * @dataProvider getDataForValidateFails
	 *
	 * @param string $handlerClassName
	 */
	public function testValidateFails(string $handlerClassName) : void
	{
		$validator = new CommandHandlerValidator();

		Assert::exception(function () use ($validator, $handlerClassName) : void {
			$validator->validate($handlerClassName);
		}, StaticAnalysisFailedException::class);
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateFails() : array
	{
		return [
			['NonexistentClass'],
			[NotFinalHandler::class],
			[MissingHandleMethodHandler::class],
			[HandleMethodNotPublicHandler::class],
			[HandleMethodHasNoParameterHandler::class],
			[HandleMethodHasMoreParametersHandler::class],
			[HandleMethodHasIncorrectlyNamedParameterHandler::class],
			[HandleMethodHasParameterWithIncorrectTypeHandler::class],
			[HandleMethodHasNoReturnTypeHandler::class],
			[HandleMethodHasIncorrectReturnTypeHandler::class],
			[HandleMethodHasNullableNewEntityIdReturnTypeHandler::class],
			[NotFinalCommandHandler::class],
			[CommandHasIncorrectNameHandler::class],
			[CommandNameDoesNotMatchHandler::class],
		];
	}

}



(new CommandHandlerValidatorTest())->run();
