<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\CommandBus\Implementation\NewEntityId;
use Damejidlo\EventBus\IEventDispatcher;
use Damejidlo\MessageBus\Handling\HandlerInvokingMiddleware;
use Damejidlo\MessageBus\Handling\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\Handling\Implementation\HandlerProviderFromStaticArray;
use Damejidlo\MessageBus\Handling\Implementation\HandlerTypesResolverFromStaticArray;
use Damejidlo\MessageBus\Handling\SplitByHandlerTypeMiddleware;
use Damejidlo\MessageBus\MiddlewareSupportingMessageBus;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\PlaceOrderCommand;
use DamejidloTests\Integration\Fixtures\PlaceOrderHandler;
use Mockery;
use Tester\Assert;



class HandlingThroughMiddlewareTest extends DjTestCase
{

	public function testHandle() : void
	{
		$eventDispatcher = Mockery::mock(IEventDispatcher::class);
		$eventDispatcher->shouldReceive('dispatch')->once();

		$handler = new PlaceOrderHandler($eventDispatcher);

		$handlerTypesResolver = new HandlerTypesResolverFromStaticArray([
			PlaceOrderCommand::class => [
				PlaceOrderHandler::class,
			],
		]);

		$handlerProvider = new HandlerProviderFromStaticArray([
			PlaceOrderHandler::class => $handler,
		]);

		$handlerInvoker = new HandlerInvoker();

		$bus = new MiddlewareSupportingMessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$command = new PlaceOrderCommand();
		$result = $bus->handle($command);

		Assert::type(NewEntityId::class, $result);
		/** @var NewEntityId $result */
		Assert::same(1, $result->toInteger());
	}

}



(new HandlingThroughMiddlewareTest())->run();
