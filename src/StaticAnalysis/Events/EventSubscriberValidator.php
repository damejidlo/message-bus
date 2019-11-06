<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Events;

use Damejidlo\MessageBus\Events\IEvent;
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



class EventSubscriberValidator
{

	private const EVENT_CLASS_NAME_SUFFIX = 'Event';
	private const SUBSCRIBER_CLASS_NAME_EVENT_PREFIX = 'On';

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
	 * @param string $subscriberClass
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $subscriberClass) : void
	{
		(new ClassExistsRule())->validate($subscriberClass);
		(new ClassIsFinalRule())->validate($subscriberClass);

		$handleMethodName = 'handle';
		(new ClassHasPublicMethodRule($handleMethodName))->validate($subscriberClass);

		$handleMethod = ReflectionHelper::requireMethodReflection($subscriberClass, $handleMethodName);
		(new MethodHasOneParameterRule())->validate($handleMethod);

		$parameter = $handleMethod->getParameters()[0];
		$parameterName = 'event';
		(new MethodParameterNameMatchesRule($parameterName))->validate($parameter);
		$parameterType = IEvent::class;
		(new MethodParameterTypeMatchesRule($parameterType))->validate($parameter);

		(new MethodReturnTypeIsSetRule())->validate($handleMethod);
		(new MethodReturnTypeIsNotNullableRule())->validate($handleMethod);
		(new MethodReturnTypeIsInRule('void'))->validate($handleMethod);

		$eventClass = $this->messageTypeExtractor->extract($subscriberClass);

		(new ClassIsFinalRule())->validate($eventClass);
		(new ClassNameHasSuffixRule(self::EVENT_CLASS_NAME_SUFFIX))->validate($eventClass);
		$eventName = $this->messageNameExtractor->extract($eventClass, self::EVENT_CLASS_NAME_SUFFIX);

		$this->validateHandlerClassName($subscriberClass, $eventName);
	}



	/**
	 * @param string $handlerClass
	 * @param string $messageName
	 * @throws StaticAnalysisFailedException
	 */
	private function validateHandlerClassName(string $handlerClass, string $messageName) : void
	{
		$expectedHandlerClassShort = sprintf(
			'#^(.+)%s%s$#',
			self::SUBSCRIBER_CLASS_NAME_EVENT_PREFIX,
			$messageName
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
