<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Events;

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\StaticAnalysis\MessageTypeExtractor;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassExistsRule;



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
	 */
	public function validate(string $subscriberClass) : void
	{
		(new ClassExistsRule())->validate($subscriberClass);

		$subscriberClassReflection = new \ReflectionClass($subscriberClass);

		$this->validateClass($subscriberClassReflection);
		$this->validateHandleMethod($subscriberClassReflection);
		$this->validateHandleMethodParameter($subscriberClassReflection);

		$eventClass = $this->messageTypeExtractor->extract($subscriberClass);
		$eventName = $this->validateEventAndExtractName($eventClass);

		$this->validateSubscriberClassName($subscriberClassReflection, $eventName);
	}



	/**
	 * @param \ReflectionClass $subscriberClassReflection
	 */
	private function validateClass(\ReflectionClass $subscriberClassReflection) : void
	{
		if (!$subscriberClassReflection->isFinal()) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" must be final.',
				$subscriberClassReflection->getName()
			));
		}
	}



	/**
	 * @param \ReflectionClass $subscriberClassReflection
	 */
	private function validateHandleMethod(\ReflectionClass $subscriberClassReflection) : void
	{
		$subscriberClass = $subscriberClassReflection->getName();

		if (!$subscriberClassReflection->hasMethod('handle')) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" must implement method "handle".',
				$subscriberClass
			));
		}

		$handleMethod = $subscriberClassReflection->getMethod('handle');

		if (!$handleMethod->isPublic()) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" method "handle" must be public.',
				$subscriberClass
			));
		}
	}



	/**
	 * @param \ReflectionClass $subscriberClassReflection
	 */
	private function validateHandleMethodParameter(\ReflectionClass $subscriberClassReflection) : void
	{
		$subscriberClass = $subscriberClassReflection->getName();

		$handleMethod = $subscriberClassReflection->getMethod('handle');

		$handleMethodParameters = $handleMethod->getParameters();

		if (count($handleMethodParameters) !== 1) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" must have method "handle" with exactly one parameter.',
				$subscriberClass
			));
		}

		$handleMethodParameter = $handleMethodParameters[0];

		if ($handleMethodParameter->getName() !== 'event') {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" method "handle" must have parameter named "event".',
				$subscriberClass
			));
		}

		$expectedHandleMethodParameterType = IEvent::class;

		if ($handleMethodParameter->getType() === NULL
			|| !is_subclass_of($handleMethodParameter->getType()->__toString(), $expectedHandleMethodParameterType)) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" method "handle" must have parameter of type "%s".',
				$subscriberClass,
				$expectedHandleMethodParameterType
			));
		}

		$handleMethodReturnType = $handleMethod->getReturnType();

		if ($handleMethodReturnType === NULL) {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" method "handle" must have return type "void", NULL found.',
				$subscriberClass
			));
		}

		if ($handleMethodReturnType->getName() !== 'void') {
			throw new InvalidSubscriberException(sprintf(
				'Event subscriber "%s" method "handle" must have return type "void", type "%s" found.',
				$subscriberClass,
				$handleMethodReturnType->getName()
			));
		}
	}



	/**
	 * @param string $eventClass
	 * @return string
	 */
	private function validateEventAndExtractName(string $eventClass) : string
	{
		$eventClassReflection = new \ReflectionClass($eventClass);
		$eventShortName = $eventClassReflection->getShortName();

		if (!$eventClassReflection->isFinal()) {
			throw new InvalidSubscriberException(sprintf(
				'Event "%s" must be final.',
				$eventClass
			));
		}

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
