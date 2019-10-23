<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Events;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\StaticAnalysis\Events\EventSubscriberValidator;
use Damejidlo\MessageBus\StaticAnalysis\Events\InvalidSubscriberException;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\DoSomethingOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\EventHasIncorrectNameOnIncorrectName;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\EventNameDoesNotMatchSubscriber;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasIncorrectReturnTypeOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasMoreParametersOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasNoParameterOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasNullReturnTypeOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodNotPublicOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\MissingHandleOnSomethingValidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\NotFinalEventOnSomethingInvalidHappened;
use DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\NotFinalOnSomethingValidHappened;
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



(new EventSubscriberValidatorTest())->run();
