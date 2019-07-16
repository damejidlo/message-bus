<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\Handling\HandlerCannotBeProvidedException;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerProvider;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\HandlerInvokingMiddleware;
use Damejidlo\MessageBus\Middleware\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use Damejidlo\MessageBus\Middleware\SplitByHandlerTypeMiddleware;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\CreateInvoiceOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\NotifyUserOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\OrderPlacedEvent;
use Tester\Assert;



class EventHandlingTest extends DjTestCase
{

	public function testHandleSucceeds() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			OrderPlacedEvent::class => [
				CreateInvoiceOnOrderPlaced::class,
				NotifyUserOnOrderPlaced::class,
			],
		]);

		$firstSubscriber = new CreateInvoiceOnOrderPlaced();
		$secondSubscriber = new NotifyUserOnOrderPlaced();

		$handlerProvider = new ArrayMapHandlerProvider([
			CreateInvoiceOnOrderPlaced::class => $firstSubscriber,
			NotifyUserOnOrderPlaced::class => $secondSubscriber,
		]);

		$handlerInvoker = new HandlerInvoker();

		$bus = new MessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$event = new OrderPlacedEvent();

		Assert::noError(function () use ($bus, $event) : void {
			$result = $bus->handle($event, MiddlewareContext::empty());
			Assert::null($result);
		});

		Assert::true($firstSubscriber->wasInvoked());
		Assert::true($secondSubscriber->wasInvoked());
	}



	public function testHandleSucceedsWithNoSubscribersConfigured() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([]);
		$handlerProvider = new ArrayMapHandlerProvider([]);
		$handlerInvoker = new HandlerInvoker();

		$bus = new MiddlewareSupportingMessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$event = new OrderPlacedEvent();

		Assert::noError(function () use ($bus, $event) : void {
			$result = $bus->handle($event, MiddlewareContext::empty());
			Assert::null($result);
		});
	}



	public function testHandleFailsWithHandlerNotProvided() : void
	{
		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			OrderPlacedEvent::class => [
				CreateInvoiceOnOrderPlaced::class,
			],
		]);
		$handlerProvider = new ArrayMapHandlerProvider([]);
		$handlerInvoker = new HandlerInvoker();

		$bus = new MiddlewareSupportingMessageBus();
		$bus->appendMiddleware(new HandlerTypesResolvingMiddleware($handlerTypesResolver));
		$bus->appendMiddleware(new SplitByHandlerTypeMiddleware());
		$bus->appendMiddleware(new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker));

		$event = new OrderPlacedEvent();

		Assert::exception(function () use ($bus, $event) : void {
			$bus->handle($event, MiddlewareContext::empty());
		}, HandlerCannotBeProvidedException::class);
	}

}



(new EventHandlingTest())->run();
