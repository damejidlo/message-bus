<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Logging;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Logging\MessageContextResolver;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Logging\Fixtures\TestBusMessage;
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
				'messageHash' => '27c835e9b99868d641c28065d6ddc6b1e856edf5',
			],
			$resolver->getContext(new TestBusMessage())
		);
	}



	public function testEmptyLoggableMessage() : void
	{
		$resolver = new MessageContextResolver();

		Assert::equal(
			[
				'messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
				'messageHash' => '2d31634f453c402c1a975379886a5a5048bc1dee',
			],
			$resolver->getContext(new TestLoggableBusMessage([]))
		);
	}



	public function testLoggableMessageWithContext() : void
	{
		$resolver = new MessageContextResolver();

		Assert::equal(
			[
				'messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
				'messageHash' => '0f7aa597b520c8cad947fc869cede7e72a56e63a',
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



	public function testLoggableMessageWithContextAndPrefixing() : void
	{
		$resolver = new MessageContextResolver(NULL, NULL, 'prefix_');

		Assert::equal(
			[
				'prefix_messageType' => 'DamejidloTests\\MessageBus\\Logging\\Fixtures\\TestLoggableBusMessage',
				'prefix_messageHash' => '0f7aa597b520c8cad947fc869cede7e72a56e63a',
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
					'messageHash' => '54b953fad1149b972ede8bd2bcba0ceae6ed639f',
					'disambiguated_messageType' => 1,
					'disambiguated_messageHash' => 1,
				],
				$resolver->getContext(new TestLoggableBusMessage([
					'messageType' => 1,
					'messageHash' => 1,
				]))
			);
		}, E_USER_WARNING, 'Message context merge failed with following duplicate keys: "messageType, messageHash"');
	}

}



(new MessageContextResolverTest())->run();
