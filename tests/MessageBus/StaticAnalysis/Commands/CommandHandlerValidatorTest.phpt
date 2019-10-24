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
	 * @param string|NULL $expectedExceptionMessage
	 */
	public function testValidateFails(string $handlerClassName, ?string $expectedExceptionMessage = NULL) : void
	{
		$validator = new CommandHandlerValidator();

		Assert::exception(function () use ($validator, $handlerClassName) : void {
			$validator->validate($handlerClassName);
		}, StaticAnalysisFailedException::class, $expectedExceptionMessage);
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateFails() : array
	{
		return [
			[
				'NonexistentClass',
				'Static analysis failed for class "NonexistentClass": '
				. 'Class does not exist',
			],
			[
				NotFinalHandler::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\NotFinalHandler": '
				. 'Class must be final.',
			],
			[
				MissingHandleMethodHandler::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\MissingHandleMethodHandler": '
				. 'Method "handle" does not exist',
			],
			[
				HandleMethodNotPublicHandler::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodNotPublicHandler": '
				. 'Method "handle" is not public',
			],
			[
				HandleMethodHasNoParameterHandler::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNoParameterHandler": '
				. 'Method "handle" must have exactly one parameter',
			],
			[
				HandleMethodHasMoreParametersHandler::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasMoreParametersHandler": '
				. 'Method "handle" must have exactly one parameter',
			],
			[
				HandleMethodHasIncorrectlyNamedParameterHandler::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasIncorrectlyNamedParameterHandler": '
				. 'Method parameter name must be "command"',
			],
			[
				HandleMethodHasParameterWithIncorrectTypeHandler::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasParameterWithIncorrectTypeHandler": '
				. 'Method parameter "command" must be of type "Damejidlo\MessageBus\Commands\ICommand"',
			],
			[
				HandleMethodHasNoReturnTypeHandler::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNoReturnTypeHandler": '
				. 'Method "handle" must have an explicit return type',
			],
			[
				HandleMethodHasIncorrectReturnTypeHandler::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasIncorrectReturnTypeHandler": '
				. 'Method "handle" return type must be in [void, Damejidlo\MessageBus\Commands\NewEntityId]',
			],
			[
				HandleMethodHasNullableNewEntityIdReturnTypeHandler::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Commands\Fixtures\HandleMethodHasNullableNewEntityIdReturnTypeHandler": '
				. 'Method "handle" return type must not be nullable',
			],
			[NotFinalCommandHandler::class],
			[CommandHasIncorrectNameHandler::class],
			[CommandNameDoesNotMatchHandler::class],
		];
	}

}



(new CommandHandlerValidatorTest())->run();
