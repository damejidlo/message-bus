<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\MessageBus\Middleware;

require_once __DIR__ . '/../../bootstrap.php';

use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use DamejidloTests\DjTestCase;
use Tester\Assert;



class MiddlewareContextTest extends DjTestCase
{

	public function testStoreValue() : void
	{
		$context = MiddlewareContext::empty();

		$key = 'foo';
		$value = new \stdClass();

		$context = $context->with($key, $value);

		Assert::true($context->has($key));
		Assert::same($value, $context->get($key));
	}



	public function testStoreValueByType() : void
	{
		$context = MiddlewareContext::empty();

		$firstObject = new \stdClass();
		$secondObject = new \stdClass();

		Assert::notSame($firstObject, $secondObject);

		$context = $context->withValueStoredByType($firstObject);
		$context = $context->withValueStoredByType($secondObject);

		Assert::true($context->has(\stdClass::class));
		Assert::same($secondObject, $context->getByType(\stdClass::class));
	}



	public function testGetObjectByTypeFailsOnUnexpectedValueType() : void
	{
		$context = MiddlewareContext::empty();

		$context = $context->with(\stdClass::class, 'string-value');

		Assert::exception(
			function () use ($context) : void {
				$context->getByType(\stdClass::class);
			},
			\LogicException::class
		);
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



(new MiddlewareContextTest())->run();
