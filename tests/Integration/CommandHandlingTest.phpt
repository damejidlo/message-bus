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
use Damejidlo\MessageBus\Handling\HandlerNotFoundException;
use Damejidlo\MessageBus\Handling\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerProvider;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\Handling\SplitByHandlerTypeMiddleware;
use Damejidlo\MessageBus\MiddlewareSupportingMessageBus;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\PlaceOrderCommand;
use DamejidloTests\Integration\Fixtures\PlaceOrderHandler;
use Mockery;
use Tester\Assert;



class CommandHandlingTest extends DjTestCase
{

	public function testHandleSucceedsWithEvents() : void
	{
		$eventDispatcher = Mockery::mock(IEventDispatcher::class);
		$eventDispatcher->shouldReceive('dispatch')->once();

		$handler = new PlaceOrderHandler($eventDispatcher);

		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			PlaceOrderCommand::class => [
				PlaceOrderHandler::class,
			],
		]);

		$handlerProvider = new ArrayMapHandlerProvider([
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



	public function testHandleFailsWithHandlerNotFound() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([]);
		$handlerProvider = new ArrayMapHandlerProvider([]);
		$handlerInvoker = new HandlerInvoker();

		$bus = new MiddlewareSupportingMessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$command = new PlaceOrderCommand();

		Assert::exception(function () use ($bus, $command) : void {
			$bus->handle($command);
		}, HandlerNotFoundException::class);
	}

}



(new CommandHandlingTest())->run();
