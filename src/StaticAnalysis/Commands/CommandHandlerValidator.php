<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Commands;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\NewEntityId;



class CommandHandlerValidator
{

	private const COMMAND_CLASS_NAME_SUFFIX = 'Command';
	private const HANDLER_CLASS_NAME_SUFFIX = 'Handler';

	/**
	 * @var CommandTypeExtractor
	 */
	private $commandTypeExtractor;



	/**
	 * @param CommandTypeExtractor $commandTypeExtractor
	 */
	public function __construct(?CommandTypeExtractor $commandTypeExtractor = NULL)
	{
		$this->commandTypeExtractor = $commandTypeExtractor ?? new CommandTypeExtractor();
	}



	/**
	 * @param string $handlerClass
	 */
	public function validate(string $handlerClass) : void
	{
		$handlerClassReflection = new \ReflectionClass($handlerClass);

		$this->validateClass($handlerClassReflection);
		$this->validateHandleMethod($handlerClassReflection);
		$this->validateHandleMethodParameter($handlerClassReflection);

		$commandClass = $this->commandTypeExtractor->extract($handlerClass);
		$commandName = $this->validateCommandAndExtractName($commandClass, $handlerClass);

		$this->validateHandlerClassName($handlerClassReflection, $commandName);
	}



	/**
	 * @param \ReflectionClass $handlerClassReflection
	 */
	private function validateClass(\ReflectionClass $handlerClassReflection) : void
	{
		$handlerClass = $handlerClassReflection->getName();

		if (!$handlerClassReflection->isFinal()) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" must be final.',
				$handlerClass
			));
		}
	}



	/**
	 * @param \ReflectionClass $handlerClassReflection
	 */
	private function validateHandleMethod(\ReflectionClass $handlerClassReflection) : void
	{
		$handlerClass = $handlerClassReflection->getName();

		if (!$handlerClassReflection->hasMethod('handle')) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" must implement method "handle".',
				$handlerClass
			));
		}

		$handleMethod = $handlerClassReflection->getMethod('handle');

		if (!$handleMethod->isPublic()) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" method "handle" must be public.',
				$handlerClass
			));
		}
	}



	/**
	 * @param \ReflectionClass $handlerClassReflection
	 */
	private function validateHandleMethodParameter(\ReflectionClass $handlerClassReflection) : void
	{
		$handlerClass = $handlerClassReflection->getName();

		$handleMethod = $handlerClassReflection->getMethod('handle');

		$handleMethodParameters = $handleMethod->getParameters();

		if (count($handleMethodParameters) !== 1) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" must have method "handle" with exactly one parameter.',
				$handlerClass
			));
		}

		$handleMethodParameter = $handleMethodParameters[0];

		if ($handleMethodParameter->getName() !== 'command') {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" method "handle" must have parameter named "command".',
				$handlerClass
			));
		}

		$expectedHandleMethodParameterType = ICommand::class;

		if ($handleMethodParameter->getType() === NULL
			|| !is_subclass_of($handleMethodParameter->getType()->getName(), $expectedHandleMethodParameterType)) {
			throw new InvalidHandlerException(sprintf(
				'Command handler "%s" method "handle" must have parameter of type "%s".',
				$handlerClass,
				$expectedHandleMethodParameterType
			));
		}

		$this->validateHandleMethodReturnType($handleMethod->getReturnType(), $handlerClass);
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

		if (!$commandClassReflection->isFinal()) {
			throw new InvalidHandlerException(sprintf(
				'Command "%s" must be final.',
				$commandClass
			));
		}

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
