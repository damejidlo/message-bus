<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\Logging\MessageContextResolver;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestBusMessage;
use DamejidloTests\MessageBus\Logging\Fixtures\TestEvent;
use DamejidloTests\MessageBus\Logging\Fixtures\TestLoggableBusMessage;
use Tester\Assert;



/**
 * @testCase
 */
class MessageContextResolverTest extends DjTestCase
{

	public function testRegularMessage() : void
	{
		$resolver = new MessageContextResolver();

		Assert::equal(
			[
				'messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestBusMessage',
			],
			$resolver->getContext(new TestBusMessage())
		);
	}



	public function testSubscriberSpecificDomainEvent() : void
	{
		$resolver = new MessageContextResolver();

		$message  = new SubscriberSpecificDomainEvent(new TestEvent(), 'someSubscriberType');

		Assert::equal(
			[
				'eventType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestEvent',
				'subscriberType' => 'someSubscriberType',
			],
			$resolver->getContext($message)
		);
	}



	public function testMessageWithProperties() : void
	{
		$resolver = new MessageContextResolver();

		Assert::equal(
			[
				'messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
				'integerAttribute' => 1,
				'stringAttribute' => 'string',
				'arrayAttribute' => [
					'nestedAttribute' => 'nested',
				],
			],
			$resolver->getContext(new TestLoggableBusMessage([
				'integerAttribute' => 1,
				'stringAttribute' => 'string',
				'arrayAttribute' => [
					'nestedAttribute' => 'nested',
				],
			]))
		);
	}



	public function testPrefixing() : void
	{
		$resolver = new MessageContextResolver(NULL, NULL, NULL, 'prefix_');

		Assert::equal(
			[
				'prefix_messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
				'prefix_integerAttribute' => 1,
				'prefix_stringAttribute' => 'string',
				'prefix_arrayAttribute' => [
					'nestedAttribute' => 'nested',
				],
			],
			$resolver->getContext(new TestLoggableBusMessage([
				'integerAttribute' => 1,
				'stringAttribute' => 'string',
				'arrayAttribute' => [
					'nestedAttribute' => 'nested',
				],
			]))
		);
	}



	public function testLoggableMessageWithCollidingContext() : void
	{
		$resolver = new MessageContextResolver();

		Assert::error(function () use ($resolver) : void {
			Assert::equal(
				[
					'messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
					'disambiguated_messageType' => 1,
					'uniqueKey' => 1,
				],
				$resolver->getContext(new TestLoggableBusMessage([
					'messageType' => 1,
					'uniqueKey' => 1,
				]))
			);
		}, E_USER_WARNING, 'Message context merge failed with following duplicate keys: "messageType"');
	}

}



(new MessageContextResolverTest())->run();
