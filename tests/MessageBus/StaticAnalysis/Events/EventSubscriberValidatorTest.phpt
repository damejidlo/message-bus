<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Events;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\IEventSubscriber;
use Damejidlo\MessageBus\StaticAnalysis\Events\EventSubscriberValidator;
use Damejidlo\MessageBus\StaticAnalysis\Events\InvalidSubscriberException;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class EventSubscriberValidatorTest extends DjTestCase
{

	public function testValidateSucceeds() : void
	{
		$validator = new EventSubscriberValidator();

		Assert::noError(function () use ($validator) : void {
			$validator->validate(DoSomethingOnSomethingValidHappened::class);
		});
	}



	/**
	 * @dataProvider getDataForValidateFails
	 *
	 * @param string $subscriberClassName
	 */
	public function testValidateFails(string $subscriberClassName) : void
	{
		$validator = new EventSubscriberValidator();

		Assert::exception(function () use ($validator, $subscriberClassName) : void {
			$validator->validate($subscriberClassName);
		}, InvalidSubscriberException::class);
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateFails() : array
	{
		return [
			[NotFinalOnSomethingValidHappened::class],
			[MissingHandleOnSomethingValidHappened::class],
			[HandleMethodNotPublicOnSomethingValidHappened::class],
			[HandleMethodHasNoParameterOnSomethingValidHappened::class],
			[HandleMethodHasMoreParametersOnSomethingValidHappened::class],
			[HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened::class],
			[HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened::class],
			[HandleMethodHasNullReturnTypeOnSomethingValidHappened::class],
			[HandleMethodHasIncorrectReturnTypeOnSomethingValidHappened::class],
			[NotFinalEventOnSomethingInvalidHappened::class],
			[EventHasIncorrectNameOnIncorrectName::class],
			[EventNameDoesNotMatchSubscriber::class],
		];
	}

}



/**
 * @see ValidSubscriber
 */
final class SomethingValidHappenedEvent implements IEvent
{

}



final class DoSomethingOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}



class NotFinalOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}



final class MissingHandleOnSomethingValidHappened implements IEventSubscriber
{

}



final class HandleMethodNotPublicOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	protected function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}



final class HandleMethodHasNoParameterOnSomethingValidHappened implements IEventSubscriber
{

	public function handle() : void
	{
	}

}



final class HandleMethodHasMoreParametersOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param mixed $foo
	 * @param mixed $bar
	 */
	public function handle($foo, $bar) : void
	{
	}

}



final class HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $foo
	 */
	public function handle(SomethingValidHappenedEvent $foo) : void
	{
	}

}



final class HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param string $event
	 */
	public function handle(string $event) : void
	{
	}

}



final class HandleMethodHasNullReturnTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event)
	{
	}

}



final class HandleMethodHasIncorrectReturnTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 * @return string
	 */
	public function handle(SomethingValidHappenedEvent $event) : string
	{
		return '';
	}

}



class SomethingInvalidHappenedEvent implements IEvent
{

}



final class NotFinalEventOnSomethingInvalidHappened implements IEventSubscriber
{

	/**
	 * @param SomethingInvalidHappenedEvent $event
	 */
	public function handle(SomethingInvalidHappenedEvent $event) : void
	{
	}

}



final class IncorrectName implements IEvent
{

}



final class EventHasIncorrectNameOnIncorrectName implements IEventSubscriber
{

	/**
	 * @param IncorrectName $event
	 */
	public function handle(IncorrectName $event) : void
	{
	}

}



final class EventNameDoesNotMatchSubscriber implements IEventSubscriber
{

	/**
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event) : void
	{
	}

}

(new EventSubscriberValidatorTest())->run();
