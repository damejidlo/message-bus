<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;




class MiddlewareCallbackChainCreatorTest extends DjTestCase
{

	public const FIRST_MIDDLEWARE_BEFORE = 'first-before';
	public const FIRST_MIDDLEWARE_AFTER = 'first-after';
	public const END_CHAIN_CALLBACK = 'end-chain-callback';
	public const SECOND_MIDDLEWARE_BEFORE = 'second-before';
	public const SECOND_MIDDLEWARE_AFTER = 'second-after';

	private const RETURN_VALUE = 42;



	public function testCreate() : void
	{
		$creator = new MiddlewareCallbackChainCreator();

		$log = new Log();

		$middleware = [
			new FirstMiddleware($log),
			new SecondMiddleware($log),
		];

		$message = $this->mockBusMessage();

		$endChainWithCallback = function ($actualMessage) use ($log, $message) {
			$log->log[] = MiddlewareCallbackChainCreatorTest::END_CHAIN_CALLBACK;
			Assert::same($message, $actualMessage);

			return self::RETURN_VALUE;
		};


		$callback = $creator->create($middleware, $endChainWithCallback);
		$result = $callback($message);

		Assert::same(self::RETURN_VALUE, $result);

		Assert::same([
			self::FIRST_MIDDLEWARE_BEFORE,
			self::SECOND_MIDDLEWARE_BEFORE,
			self::END_CHAIN_CALLBACK,
			self::SECOND_MIDDLEWARE_AFTER,
			self::FIRST_MIDDLEWARE_AFTER,
		], $log->log);
	}



	/**
	 * @return IBusMessage|MockInterface
	 */
	private function mockBusMessage() : IBusMessage
	{
		$mock = Mockery::mock(IBusMessage::class);

		return $mock;
	}

}



class Log
{

	/**
	 * @var mixed[]
	 */
	public $log = [];

}



class FirstMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var Log
	 */
	private $log;



	public function __construct(Log $log)
	{
		$this->log = $log;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$this->log->log[] = MiddlewareCallbackChainCreatorTest::FIRST_MIDDLEWARE_BEFORE;

		$result = $nextMiddlewareCallback($message);

		$this->log->log[] = MiddlewareCallbackChainCreatorTest::FIRST_MIDDLEWARE_AFTER;

		return $result;
	}

}



class SecondMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var Log
	 */
	private $log;



	public function __construct(Log $log)
	{
		$this->log = $log;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, \Closure $nextMiddlewareCallback)
	{
		$this->log->log[] = MiddlewareCallbackChainCreatorTest::SECOND_MIDDLEWARE_BEFORE;

		$result = $nextMiddlewareCallback($message);

		$this->log->log[] = MiddlewareCallbackChainCreatorTest::SECOND_MIDDLEWARE_AFTER;

		return $result;
	}

}



(new MiddlewareCallbackChainCreatorTest())->run();
