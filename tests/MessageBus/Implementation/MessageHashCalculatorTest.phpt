<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Implementation\MessageHashCalculator;
use DamejidloTests\DjTestCase;
use DamejidloTests\MessageBus\Implementation\Fixtures\TestMessage;
use DamejidloTests\MessageBus\Implementation\Fixtures\TestMessageWithDifferentAttributes;
use DamejidloTests\MessageBus\Implementation\Fixtures\TestMessageWithDifferentName;
use Tester\Assert;



/**
 * @testCase
 */
class MessageHashCalculatorTest extends DjTestCase
{

	public function testCalculatorIsDeterministic() : void
	{
		$calculator = new MessageHashCalculator();

		$message1 = new TestMessage(new \DateTimeImmutable('2019-01-01 13:00:00'));
		$firstResult = $calculator->calculateHash($message1);

		$message2 = new TestMessage(new \DateTimeImmutable('2019-01-01 13:00:00'));
		$secondResult = $calculator->calculateHash($message2);

		Assert::same($firstResult, $secondResult);
	}



	public function testCalculatorReturnsDifferentResultForDifferentTypes() : void
	{
		$calculator = new MessageHashCalculator();

		$message1 = new TestMessage(new \DateTimeImmutable('2019-01-01 13:00:00'));
		$firstResult = $calculator->calculateHash($message1);

		$message2 = new TestMessageWithDifferentName(new \DateTimeImmutable('2019-01-01 13:00:00'));
		$secondResult = $calculator->calculateHash($message2);

		Assert::notSame($firstResult, $secondResult);
	}



	public function testCalculatorReturnsDifferentResultForDifferentAttributes() : void
	{
		$calculator = new MessageHashCalculator();

		$message1 = new TestMessage(new \DateTimeImmutable('2019-01-01 13:00:00'));
		$firstResult = $calculator->calculateHash($message1);

		$message2 = new TestMessageWithDifferentAttributes();
		$secondResult = $calculator->calculateHash($message2);

		Assert::notSame($firstResult, $secondResult);
	}

}



(new MessageHashCalculatorTest())->run();
