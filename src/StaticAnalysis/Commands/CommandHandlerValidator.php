<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Commands;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\StaticAnalysis\MessageTypeExtractor;
use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassExistsRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassHasPublicMethodRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassIsFinalRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodHasOneParameterRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterNameMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterTypeMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class CommandHandlerValidator
{

	private const COMMAND_CLASS_NAME_SUFFIX = 'Command';
	private const HANDLER_CLASS_NAME_SUFFIX = 'Handler';

	/**
	 * @var MessageTypeExtractor
	 */
	private $messageTypeExtractor;



	public function __construct(?MessageTypeExtractor $messageTypeExtractor = NULL)
	{
		$this->messageTypeExtractor = $messageTypeExtractor ?? new MessageTypeExtractor();
	}



	/**
	 * @param string $handlerClass
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $handlerClass) : void
	{
		(new ClassExistsRule())->validate($handlerClass);
		(new ClassIsFinalRule())->validate($handlerClass);

		$handleMethodName = 'handle';
		(new ClassHasPublicMethodRule($handleMethodName))->validate($handlerClass);

		$handleMethod = ReflectionHelper::requireMethodReflection($handlerClass, $handleMethodName);
		(new MethodHasOneParameterRule())->validate($handleMethod);

		$parameter = $handleMethod->getParameters()[0];
		$parameterName = 'command';
		(new MethodParameterNameMatchesRule($parameterName))->validate($parameter);
		$parameterType = ICommand::class;
		(new MethodParameterTypeMatchesRule($parameterType))->validate($parameter);

		$handlerClassReflection = new \ReflectionClass($handlerClass);
		$this->validateHandleMethodReturnType($handleMethod->getReturnType(), $handlerClass);

		$commandClass = $this->messageTypeExtractor->extract($handlerClass);
		$commandName = $this->validateCommandAndExtractName($commandClass, $handlerClass);

		$this->validateHandlerClassName($handlerClassReflection, $commandName);
	}



	private function validateHandleMethodReturnType(?\ReflectionType $handleMethodReturnType, string $handlerClass) : void
	{
		if ($handleMethodReturnType === NULL) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" method "handle" must have return type "void" or "%s", NULL found.',
				$handlerClass,
				NewEntityId::class
			));
		}

		$handleMethodReturnTypeName = (string) $handleMethodReturnType;

		if ($handleMethodReturnTypeName === 'void') {
			return;
		}

		if ($handleMethodReturnTypeName === NewEntityId::class) {
			if ($handleMethodReturnType->allowsNull()) {
				throw new InvalidHandlerException(sprintf(
					'Command handler "%s" method "handle" return type "%s" must not be nullable.',
					$handlerClass,
					NewEntityId::class
				));
			}

			return;
		}

		throw new InvalidHandlerException(sprintf(
			'Command handler "%s" method "handle" must have return type "void" or non-nullable "%s", type "%s" found.',
			$handlerClass,
			NewEntityId::class,
			$handleMethodReturnTypeName
		));
	}



	/**
	 * @param string $commandClass
	 * @param string $handlerClass
	 * @return string
	 */
	private function validateCommandAndExtractName(string $commandClass, string $handlerClass) : string
	{
		$commandClassReflection = new \ReflectionClass($commandClass);
		$commandShortName = $commandClassReflection->getShortName();

		(new ClassIsFinalRule())->validate($commandClass);

		$pattern = sprintf('#^(.*)%s$#', self::COMMAND_CLASS_NAME_SUFFIX);
		if (!preg_match($pattern, $commandShortName, $matches)) {
			throw new InvalidHandlerException(sprintf(
				'Command "%s" class must be named "<command-name>%s".',
				$commandClass,
				self::COMMAND_CLASS_NAME_SUFFIX
			));
		}

		return $matches[1];
	}



	/**
	 * @param \ReflectionClass $handlerClassReflection
	 * @param string $commandName
	 */
	private function validateHandlerClassName(\ReflectionClass $handlerClassReflection, string $commandName) : void
	{
		$handlerClass = $handlerClassReflection->getName();

		$handlerClassShort = $handlerClassReflection->getShortName();

		$expectedHandlerClassShort = $commandName . self::HANDLER_CLASS_NAME_SUFFIX;

		if ($expectedHandlerClassShort !== $handlerClassShort) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" class name must match command name. Expected name: "%s".',
				$handlerClass,
				$expectedHandlerClassShort
			));
		}
	}

}
