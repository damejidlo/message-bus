<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;
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



final class ConfigurableHandlerValidator implements IMessageHandlerValidator
{

	/**
	 * @var MessageHandlerValidationConfigurations
	 */
	private $configurations;

	/**
	 * @var MessageTypeExtractor
	 */
	private $messageTypeExtractor;



	public function __construct(
		?MessageHandlerValidationConfigurations $configurations = NULL,
		?MessageTypeExtractor $messageTypeExtractor = NULL
	) {
		$this->configurations = $configurations ?? MessageHandlerValidationConfigurations::default();
		$this->messageTypeExtractor = $messageTypeExtractor ?? new MessageTypeExtractor();
	}



	/**
	 * @param HandlerType $handlerType
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(HandlerType $handlerType) : void
	{
		$handlerClass = $handlerType->toString();

		(new ClassExistsRule())->validate($handlerClass);

		$configuration = $this->configurations->get(HandlerType::fromString($handlerClass));

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

		$messageType = $this->messageTypeExtractor->extract(HandlerType::fromString($handlerClass), $handleMethodName);

		if ($configuration->messageClassMustBeFinal()) {
			(new ClassIsFinalRule())->validate($messageType->toString());
		}

		$messageClassSuffix = $configuration->messageClassSuffix();
		(new ClassNameHasSuffixRule($messageClassSuffix))->validate($messageType->toString());
		$shortMessageName = $messageType->shortName($messageClassSuffix);

		$this->validateHandlerClassName($handlerClass, $shortMessageName, $configuration);
	}



	/**
	 * @param string $handlerClass
	 * @param string $shortMessageName
	 * @param MessageHandlerValidationConfiguration $configuration
	 * @throws StaticAnalysisFailedException
	 */
	private function validateHandlerClassName(string $handlerClass, string $shortMessageName, MessageHandlerValidationConfiguration $configuration) : void
	{
		$expectedHandlerClassShort = sprintf(
			'#^%s%s%s$#',
			$configuration->handlerClassPrefixRegex(),
			$shortMessageName,
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
