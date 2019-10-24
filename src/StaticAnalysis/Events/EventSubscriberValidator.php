<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Events;

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\StaticAnalysis\MessageTypeExtractor;
use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassExistsRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassHasPublicMethodRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassIsFinalRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodHasOneParameterRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterNameMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodParameterTypeMatchesRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsInRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsNotNullableRule;
use Damejidlo\MessageBus\StaticAnalysis\Rules\MethodReturnTypeIsSetRule;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class EventSubscriberValidator
{

	private const EVENT_CLASS_NAME_SUFFIX = 'Event';
	private const SUBSCRIBER_CLASS_NAME_EVENT_PREFIX = 'On';

	/**
	 * @var MessageTypeExtractor
	 */
	private $messageTypeExtractor;



	public function __construct(?MessageTypeExtractor $messageTypeExtractor = NULL)
	{
		$this->messageTypeExtractor = $messageTypeExtractor ?? new MessageTypeExtractor();
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

		$subscriberClassReflection = new \ReflectionClass($subscriberClass);
		$eventClass = $this->messageTypeExtractor->extract($subscriberClass);
		$eventName = $this->validateEventAndExtractName($eventClass);

		$this->validateSubscriberClassName($subscriberClassReflection, $eventName);
	}



	/**
	 * @param string $eventClass
	 * @return string
	 */
	private function validateEventAndExtractName(string $eventClass) : string
	{
		$eventClassReflection = new \ReflectionClass($eventClass);
		$eventShortName = $eventClassReflection->getShortName();

		(new ClassIsFinalRule())->validate($eventClass);

		$pattern = sprintf('#^(.+)%s$#', self::EVENT_CLASS_NAME_SUFFIX);
		if (!preg_match($pattern, $eventShortName, $matches)) {
			throw new InvalidSubscriberException(sprintf(
				'Event "%s" class must be named "<event-name>%s".',
				$eventClass,
				self::EVENT_CLASS_NAME_SUFFIX
			));
		}

		return $matches[1];
	}



	/**
	 * @param \ReflectionClass $subscriberClassReflection
	 * @param string $eventName
	 */
	private function validateSubscriberClassName(\ReflectionClass $subscriberClassReflection, string $eventName) : void
	{
		$subscriberClass = $subscriberClassReflection->getName();

		$pattern = sprintf('#^(.+)%s%s$#', self::SUBSCRIBER_CLASS_NAME_EVENT_PREFIX, $eventName);
		if (!preg_match($pattern, $subscriberClass, $matches)) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" class name must match event name. Expected name: "%s".',
				$subscriberClass,
				$pattern
			));
		}
	}

}
