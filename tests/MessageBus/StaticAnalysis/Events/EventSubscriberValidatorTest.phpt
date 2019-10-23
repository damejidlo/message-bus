<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\StaticAnalysis\Events;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\MessageBus\StaticAnalysis\Events\EventSubscriberValidator;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;
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
	 * @param string|NULL $expectedExceptionMessage
	 */
	public function testValidateFails(string $subscriberClassName, ?string $expectedExceptionMessage = NULL) : void
	{
		$validator = new EventSubscriberValidator();

		Assert::exception(function () use ($validator, $subscriberClassName) : void {
			$validator->validate($subscriberClassName);
		}, StaticAnalysisFailedException::class, $expectedExceptionMessage);
	}



	/**
	 * @return mixed[][]
	 */
	public function getDataForValidateFails() : array
	{
		return [
			[
				'NonexistentClass',
				'Static analysis failed for class "NonexistentClass": '
				. 'Class does not exist',
			],
			[
				NotFinalOnSomethingValidHappened::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\NotFinalOnSomethingValidHappened": '
				. 'Class must be final.',
			],
			[
				MissingHandleOnSomethingValidHappened::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\MissingHandleOnSomethingValidHappened": '
				. 'Method "handle" does not exist',
			],
			[
				HandleMethodNotPublicOnSomethingValidHappened::class,
				'Static analysis failed for class "DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodNotPublicOnSomethingValidHappened": '
				. 'Method "handle" is not public',
			],
			[
				HandleMethodHasNoParameterOnSomethingValidHappened::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasNoParameterOnSomethingValidHappened": '
				. 'Method "handle" must have exactly one parameter',
			],
			[
				HandleMethodHasMoreParametersOnSomethingValidHappened::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasMoreParametersOnSomethingValidHappened": '
				. 'Method "handle" must have exactly one parameter',
			],
			[
				HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasIncorrectlyNamedParameterOnSomethingValidHappened": '
				. 'Method parameter name must be "event"',
			],
			[
				HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened::class,
				'Static analysis failed for class '
				. '"DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures\HandleMethodHasParameterWithIncorrectTypeOnSomethingValidHappened": '
				. 'Method parameter "event" must be of type "Damejidlo\MessageBus\Events\IEvent"',
			],
			[HandleMethodHasNullReturnTypeOnSomethingValidHappened::class],
			[HandleMethodHasIncorrectReturnTypeOnSomethingValidHappened::class],
			[NotFinalEventOnSomethingInvalidHappened::class],
			[EventHasIncorrectNameOnIncorrectName::class],
			[EventNameDoesNotMatchSubscriber::class],
		];
	}

}



(new EventSubscriberValidatorTest())->run();
