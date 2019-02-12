<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Logging\MessageTypeResolver;
use DamejidloTests\DjTestCase;
use Mockery;
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
	 * @param IBusMessage $message
	 */
	public function testGetMessageType(string $expectedMessageType, IBusMessage $message) : void
	{
		$resolver = new MessageTypeResolver();

		Assert::same($expectedMessageType, $resolver->getMessageType($message));
	}



	/**
	 * @return mixed[]
	 */
	protected function provideDataForTestGetMessageType() : array
	{
		return [
			[
				'command',
				Mockery::mock(ICommand::class),
			],
			[
				'event',
				Mockery::mock(IDomainEvent::class),
			],
			[
				'event',
				new SubscriberSpecificDomainEvent(Mockery::mock(IDomainEvent::class), 'someType'),
			],
			[
				'message', Mockery::mock(IBusMessage::class),
			],
		];
	}

}



(new MessageTypeResolverTest())->run();
