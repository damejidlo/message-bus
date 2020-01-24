<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Events\IEventSubscriber;
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



class MessageHandlerValidator
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
		(new ClassExistsRule())->validate($handlerClass);

		$configuration = $this->resolveConfiguration($handlerClass);

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

		$messageClass = $this->messageTypeExtractor->extract($handlerClass);

		if ($configuration->messageClassMustBeFinal()) {
			(new ClassIsFinalRule())->validate($messageClass);
		}

		$messageClassSuffix = $configuration->messageClassSuffix();
		(new ClassNameHasSuffixRule($messageClassSuffix))->validate($messageClass);
		$messageName = $this->messageNameExtractor->extract($messageClass, $messageClassSuffix);

		$this->validateHandlerClassName($handlerClass, $messageName, $configuration);
	}



	private function resolveConfiguration(string $handlerClass) : MessageHandlerValidationConfiguration
	{
		if (is_subclass_of($handlerClass, ICommandHandler::class)) {
			return MessageHandlerValidationConfiguration::command();
		}

		if (is_subclass_of($handlerClass, IEventSubscriber::class)) {
			return MessageHandlerValidationConfiguration::event();
		}

		throw new \LogicException(sprintf('Unsupported handler class: "%s".', $handlerClass));
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
			'#^%s%s%s$#',
			$configuration->handlerClassPrefixRegex(),
			$messageName,
			$configuration->handlerClassSuffix()
		);

		try {
			(new ShortClassNameMatchesRule($expectedHandlerClassShort))->validate($handlerClass);
		} catch (StaticAnalysisFailedException $exception) {
			throw StaticAnalysisFailedException::with(
				sprintf(
					'Message handler must match message name. Expected name: "%s"',
					$expectedHandlerClassShort
				),
				$handlerClass
			);
		}
	}

}
