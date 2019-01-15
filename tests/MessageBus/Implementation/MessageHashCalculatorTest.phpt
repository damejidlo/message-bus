<?php
declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Implementation;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Implementation\MessageHashCalculator;
use DamejidloTests\DjTestCase;
use Tester\Assert;



/**
 * @testCase
 */
class MessageHashCalculatorTest extends DjTestCase
{

	public function testCalculateHash() : void
	{
		$message = new TestMessage();
		$calculator = new MessageHashCalculator();

		Assert::same('5a89620683763773e2f00952f0dceeaba24369f0', $calculator->calculateHash($message));
	}

}


final class TestMessage implements IBusMessage
{

	/**
	 * @return mixed[]
	 */
	public function getLoggingContext() : array
	{
		return [
			'string' => 'foo',
			'array' => [
				'float' => 3.14,
				'integer' => 42,
			],
		];
	}

}


(new MessageHashCalculatorTest())->run();
