<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Commands;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\StaticAnalysis\MessageNameExtractor;
use Damejidlo\MessageBus\StaticAnalysis\MessageTypeExtractor;
use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassExistsRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassHasPublicMethodRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassIsFinalRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassNameHasSuffixRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodHasOneParameterRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterNameMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterTypeMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsInRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsNotNullableRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsSetRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ShortClassNameMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class CommandHandlerValidator
{

	private const COMMAND_CLASS_NAME_SUFFIX = 'Command';
	private const HANDLER_CLASS_NAME_SUFFIX = 'Handler';

	/**
	 * @var MessageTypeExtractor
	 */
	private $messageTypeExtractor;

	/**
	 * @var MessageNameExtractor
	 */
	private $messageNameExtractor;



	public function __construct(
		?MessageTypeExtractor $messageTypeExtractor = NULL,
		?MessageNameExtractor $messageNameExtractor = NULL
	) {
		$this->messageTypeExtractor = $messageTypeExtractor ?? new MessageTypeExtractor();
		$this->messageNameExtractor = $messageNameExtractor ?? new MessageNameExtractor();
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

		(new MethodReturnTypeIsSetRule())->validate($handleMethod);
		(new MethodReturnTypeIsNotNullableRule())->validate($handleMethod);
		(new MethodReturnTypeIsInRule('void', NewEntityId::class))->validate($handleMethod);

		$commandClass = $this->messageTypeExtractor->extract($handlerClass);

		(new ClassIsFinalRule())->validate($commandClass);
		(new ClassNameHasSuffixRule(self::COMMAND_CLASS_NAME_SUFFIX))->validate($commandClass);
		$commandName = $this->messageNameExtractor->extract($commandClass, self::COMMAND_CLASS_NAME_SUFFIX);

		$this->validateHandlerClassName($handlerClass, $commandName);
	}



	/**
	 * @param string $handlerClass
	 * @param string $messageName
	 * @throws StaticAnalysisFailedException
	 */
	private function validateHandlerClassName(string $handlerClass, string $messageName) : void
	{
		$expectedHandlerClassShort = sprintf(
			'#^%s%s$#',
			$messageName,
			self::HANDLER_CLASS_NAME_SUFFIX
		);

		try {
			(new ShortClassNameMatchesRule($expectedHandlerClassShort))->validate($handlerClass);
		} catch (StaticAnalysisFailedException $exception) {
			throw StaticAnalysisFailedException::with(
				sprintf(
					'Message handler must match command name. Expected name: "%s"',
					$expectedHandlerClassShort
				),
				$handlerClass
			);
		}
	}

}
