<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Context;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class ContextTest extends DjTestCase
{

	public function testWithAndGet() : void
	{
		$context = MiddlewareContext::empty();

		$key = 'foo';
		$value = new \stdClass();

		$context = $context->with($key, $value);

		Assert::same($value, $context->get($key));
	}



	public function testImmutability() : void
	{
		$context = MiddlewareContext::empty();

		$key = 'foo';
		$value = new \stdClass();

		$context->with($key, $value);

		Assert::exception(function () use ($context, $key) : void {
			$context->get($key);
		}, \OutOfRangeException::class);
	}

}



(new ContextTest())->run();
