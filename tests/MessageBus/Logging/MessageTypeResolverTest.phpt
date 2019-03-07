<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Logging\MessageTypeResolver;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestBusMessage;
use DamejidloTests\MessageBus\Logging\Fixtures\TestCommand;
use DamejidloTests\MessageBus\Logging\Fixtures\TestEvent;
use Tester\Assert;



/**
 * @testCase
 */
class MessageTypeResolverTest extends DjTestCase
{

	/**
	 * @dataProvider provideDataForTestGetMessageType
	 *
	 * @param string $expectedMessageType
	 * @param string $expectedSimplifiedMessageType
	 * @param IBusMessage $message
	 */
	public function testGetMessageType(string $expectedMessageType, string $expectedSimplifiedMessageType, IBusMessage $message) : void
	{
		$resolver = new MessageTypeResolver();

		Assert::same($expectedMessageType, $resolver->getMessageType($message));
		Assert::same($expectedSimplifiedMessageType, $resolver->getSimplifiedMessageType($message));
	}



	/**
	 * @return mixed[]
	 */
	protected function provideDataForTestGetMessageType() : array
	{
		return [
			[
				TestCommand::class,
				'command',
				new TestCommand(),
			],
			[
				TestEvent::class,
				'event',
				new TestEvent(),
			],
			[
				TestEvent::class,
				'event',
				new SubscriberSpecificDomainEvent(new TestEvent(), 'someType'),
			],
			[
				TestBusMessage::class,
				'message',
				new TestBusMessage(),
			],
		];
	}

}



(new MessageTypeResolverTest())->run();
