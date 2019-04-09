<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Logging\LogMessageResolver;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



/**
 * @testCase
 */
class LogMessageResolverTest extends DjTestCase
{

	/**
	 * @dataProvider provideDataForTestAllMethods
	 *
	 * @param string $messageType
	 * @param string $expectedHandlingStartedMessage
	 * @param string $expectedHandlingEndedSuccessfullyMessage
	 * @param string $expectedHandlingEndedWithErrorMessage
	 */
	public function testAllMethods(
		string $messageType,
		string $expectedHandlingStartedMessage,
		string $expectedHandlingEndedSuccessfullyMessage,
		string $expectedHandlingEndedWithErrorMessage
	) : void {
		$resolver = new LogMessageResolver();

		/** @var IBusMessage|MockInterface $message */
		$message = Mockery::mock($messageType);

		Assert::same($expectedHandlingStartedMessage, $resolver->getHandlingStartedMessage($message));
		Assert::same($expectedHandlingEndedSuccessfullyMessage, $resolver->getHandlingEndedSuccessfullyMessage($message));
		Assert::same($expectedHandlingEndedWithErrorMessage, $resolver->getHandlingEndedWithErrorMessage($message, new \Exception('exception-message')));
	}



	/**
	 * @return mixed[]
	 */
	protected function provideDataForTestAllMethods() : array
	{
		return [
			[
				'messageType' => IBusMessage::class,
				'expectedHandlingStartedMessage' => 'Message handling started.',
				'expectedHandlingEndedSuccessfullyMessage' => 'Message handling ended successfully.',
				'expectedHandlingEndedWithErrorMessage' => 'Message handling ended with error: exception-message',
			],
			[
				'messageType' => ICommand::class,
				'expectedHandlingStartedMessage' => 'Command handling started.',
				'expectedHandlingEndedSuccessfullyMessage' => 'Command handling ended successfully.',
				'expectedHandlingEndedWithErrorMessage' => 'Command handling ended with error: exception-message',
			],
			[
				'messageType' => IDomainEvent::class,
				'expectedHandlingStartedMessage' => 'Event handling started.',
				'expectedHandlingEndedSuccessfullyMessage' => 'Event handling ended successfully.',
				'expectedHandlingEndedWithErrorMessage' => 'Event handling ended with error: exception-message',
			],
		];
	}



	public function testSubscriberSpecificDomainEvent() : void
	{
		$resolver = new LogMessageResolver();

		/** @var IBusMessage|MockInterface $message */
		$event = Mockery::mock(IDomainEvent::class);

		$message = new SubscriberSpecificDomainEvent($event, 'some-subscriber-type');

		Assert::same('Event handling in subscriber started.', $resolver->getHandlingStartedMessage($message));
		Assert::same('Event handling in subscriber ended successfully.', $resolver->getHandlingEndedSuccessfullyMessage($message));
		Assert::same(
			'Event handling in subscriber ended with error: exception-message',
			$resolver->getHandlingEndedWithErrorMessage($message, new \Exception('exception-message'))
		);
	}

}



(new LogMessageResolverTest())->run();
