<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\Events\IEvent;
use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\Logging\LogMessageResolver;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
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

		/** @var IMessage|MockInterface $message */
		$message = Mockery::mock($messageType);

		$context = MiddlewareContext::empty();

		Assert::same($expectedHandlingStartedMessage, $resolver->getHandlingStartedMessage($message, $context));
		Assert::same($expectedHandlingEndedSuccessfullyMessage, $resolver->getHandlingEndedSuccessfullyMessage($message, $context));
		Assert::same(
			$expectedHandlingEndedWithErrorMessage,
			$resolver->getHandlingEndedWithErrorMessage($message, $context, new \Exception('exception-message'))
		);
	}



	/**
	 * @return mixed[]
	 */
	protected function provideDataForTestAllMethods() : array
	{
		return [
			[
				'messageType' => IMessage::class,
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
				'messageType' => IEvent::class,
				'expectedHandlingStartedMessage' => 'Event handling started.',
				'expectedHandlingEndedSuccessfullyMessage' => 'Event handling ended successfully.',
				'expectedHandlingEndedWithErrorMessage' => 'Event handling ended with error: exception-message',
			],
		];
	}



	public function testEventWithResolvedHandler() : void
	{
		$resolver = new LogMessageResolver();

		/** @var IMessage|MockInterface $message */
		$message = Mockery::mock(IEvent::class);

		$context = MiddlewareContext::empty()
			->withValueStoredByType(HandlerType::fromString('SomeHandlerType'));

		Assert::same('Event handling in subscriber started.', $resolver->getHandlingStartedMessage($message, $context));
		Assert::same('Event handling in subscriber ended successfully.', $resolver->getHandlingEndedSuccessfullyMessage($message, $context));
		Assert::same(
			'Event handling in subscriber ended with error: exception-message',
			$resolver->getHandlingEndedWithErrorMessage($message, $context, new \Exception('exception-message'))
		);
	}

}



(new LogMessageResolverTest())->run();
