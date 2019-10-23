<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Commands;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\StaticAnalysis\Commands\CommandHandlerValidator;
use Damejidlo\MessageBus\StaticAnalysis\Commands\InvalidHandlerException;
use DamejidloTests\DjTestCase;
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
		}, InvalidHandlerException::class);
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateFails() : array
	{
		return [
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



final class ValidCommand implements ICommand
{

}



final class ValidHandler implements ICommandHandler
{

	public function handle(ValidCommand $command) : void
	{
	}

}



final class ValidWithNewEntityIdReturnTypeCommand implements ICommand
{

}



final class ValidWithNewEntityIdReturnTypeHandler implements ICommandHandler
{

	public function handle(ValidWithNewEntityIdReturnTypeCommand $command) : NewEntityId
	{
		return new NewEntityId('42');
	}

}



class NotFinalHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	public function handle(ValidCommand $command) : void
	{
	}

}



final class MissingHandleMethodHandler implements ICommandHandler
{

}



final class HandleMethodNotPublicHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	protected function handle(ValidCommand $command) : void
	{
	}

}



final class HandleMethodHasNoParameterHandler implements ICommandHandler
{

	public function handle() : void
	{
	}

}



final class HandleMethodHasMoreParametersHandler implements ICommandHandler
{

	/**
	 * @param mixed $foo
	 * @param mixed $bar
	 */
	public function handle($foo, $bar) : void
	{
	}

}



final class HandleMethodHasIncorrectlyNamedParameterHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $foo
	 */
	public function handle(ValidCommand $foo) : void
	{
	}

}



final class HandleMethodHasParameterWithIncorrectTypeHandler implements ICommandHandler
{

	/**
	 * @param string $command
	 */
	public function handle(string $command) : void
	{
	}

}



final class HandleMethodHasNoReturnTypeHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	public function handle(ValidCommand $command) : void
	{
	}

}



final class HandleMethodHasIncorrectReturnTypeHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 * @return string
	 */
	public function handle(ValidCommand $command) : string
	{
		return '';
	}

}



final class HandleMethodHasNullableNewEntityIdReturnTypeHandler implements ICommandHandler
{

	public function handle(ValidCommand $command) : ?NewEntityId
	{
		return NULL;
	}

}



class NotFinalCommand implements ICommand
{

}



final class NotFinalCommandHandler implements ICommandHandler
{

	/**
	 * @param NotFinalCommand $command
	 */
	public function handle(NotFinalCommand $command) : void
	{
	}

}



final class IncorrectName implements ICommand
{

}



final class CommandHasIncorrectNameHandler implements ICommandHandler
{

	/**
	 * @param IncorrectName $command
	 */
	public function handle(IncorrectName $command) : void
	{
	}

}



final class CommandNameDoesNotMatchHandler implements ICommandHandler
{

	/**
	 * @param ValidCommand $command
	 */
	public function handle(ValidCommand $command) : void
	{
	}

}



(new CommandHandlerValidatorTest())->run();
