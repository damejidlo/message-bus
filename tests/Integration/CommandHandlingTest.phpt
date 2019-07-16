<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\CommandBus\NewEntityId;
use Damejidlo\EventBus\IEventDispatcher;
use Damejidlo\MessageBus\Handling\HandlerCannotBeProvidedException;
use Damejidlo\MessageBus\Handling\HandlerRequiredAndNotConfiguredException;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerProvider;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\HandlerInvokingMiddleware;
use Damejidlo\MessageBus\Middleware\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use Damejidlo\MessageBus\Middleware\SplitByHandlerTypeMiddleware;
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

		$bus = new MessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$command = new PlaceOrderCommand();
		$result = $bus->handle($command, MiddlewareContext::empty());

		Assert::type(NewEntityId::class, $result);
		/** @var NewEntityId $result */
		Assert::same(1, $result->toInteger());
	}



	public function testHandleFailsWithHandlerNotConfigured() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([]);
		$handlerProvider = new ArrayMapHandlerProvider([]);
		$handlerInvoker = new HandlerInvoker();

		$bus = new MessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$command = new PlaceOrderCommand();

		Assert::exception(function () use ($bus, $command) : void {
			$bus->handle($command, MiddlewareContext::empty());
		}, HandlerRequiredAndNotConfiguredException::class);
	}



	public function testHandleFailsWithHandlerNotProvided() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			PlaceOrderCommand::class => [
				PlaceOrderHandler::class,
			],
		]);
		$handlerProvider = new ArrayMapHandlerProvider([]);
		$handlerInvoker = new HandlerInvoker();

		$bus = new MessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$command = new PlaceOrderCommand();

		Assert::exception(function () use ($bus, $command) : void {
			$bus->handle($command, MiddlewareContext::empty());
		}, HandlerCannotBeProvidedException::class);
	}

}



(new CommandHandlingTest())->run();
