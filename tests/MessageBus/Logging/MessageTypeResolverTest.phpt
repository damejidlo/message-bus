<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
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

	public function testCalculatorIsDeterministic() : void
	{
		$resolver = new MessageTypeResolver();

		Assert::same('command', $resolver->getMessageType(Mockery::mock(ICommand::class)));
		Assert::same('event', $resolver->getMessageType(Mockery::mock(IDomainEvent::class)));
		Assert::same('message', $resolver->getMessageType(Mockery::mock(IBusMessage::class)));
	}

}



(new MessageTypeResolverTest())->run();
