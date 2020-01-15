<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Commands;

use Damejidlo\MessageBus\StaticAnalysis\MessageHandlerValidationConfiguration;
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
		$configuration = MessageHandlerValidationConfiguration::command();

		(new ClassExistsRule())->validate($handlerClass);

		if ($configuration->handlerClassMustBeFinal()) {
			(new ClassIsFinalRule())->validate($handlerClass);
		}

		$handleMethodName = $configuration->handleMethodName();
		(new ClassHasPublicMethodRule($handleMethodName))->validate($handlerClass);

		$handleMethod = ReflectionHelper::requireMethodReflection($handlerClass, $handleMethodName);
		(new MethodHasOneParameterRule())->validate($handleMethod);

		$parameter = $handleMethod->getParameters()[0];
		$parameterName = $configuration->handleMethodParameterName();
		(new MethodParameterNameMatchesRule($parameterName))->validate($parameter);
		$parameterType = $configuration->getHandleMethodParameterType();
		(new MethodParameterTypeMatchesRule($parameterType))->validate($parameter);

		(new MethodReturnTypeIsSetRule())->validate($handleMethod);
		(new MethodReturnTypeIsNotNullableRule())->validate($handleMethod);
		$handleMethodAllowedReturnTypes = $configuration->handleMethodAllowedReturnTypes();
		(new MethodReturnTypeIsInRule(...$handleMethodAllowedReturnTypes))->validate($handleMethod);

		$commandClass = $this->messageTypeExtractor->extract($handlerClass);

		(new ClassIsFinalRule())->validate($commandClass);
		$messageClassSuffix = $configuration->messageClassSuffix();
		(new ClassNameHasSuffixRule($messageClassSuffix))->validate($commandClass);
		$commandName = $this->messageNameExtractor->extract($commandClass, $messageClassSuffix);

		$this->validateHandlerClassName($handlerClass, $commandName, $configuration);
	}



	/**
	 * @param string $handlerClass
	 * @param string $messageName
	 * @param MessageHandlerValidationConfiguration $configuration
	 * @throws StaticAnalysisFailedException
	 */
	private function validateHandlerClassName(string $handlerClass, string $messageName, MessageHandlerValidationConfiguration $configuration) : void
	{
		$expectedHandlerClassShort = sprintf(
			'#^%s%s$#',
			$messageName,
			$configuration->handlerClassSuffix()
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
