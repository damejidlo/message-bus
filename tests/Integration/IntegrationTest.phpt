<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\CommandBus\CommandBus;
use Damejidlo\CommandBus\ICommandBus;
use Damejidlo\CommandBus\Implementation\CommandHandlerResolver;
use Damejidlo\CommandBus\Implementation\NewEntityId;
use Damejidlo\CommandBus\Middleware\EventDispatchingMiddleware;
use Damejidlo\CommandBus\Middleware\HandlerResolvingMiddleware;
use Damejidlo\EventBus\EventBus;
use Damejidlo\EventBus\Implementation\CommandBusAwareEventDispatcher;
use Damejidlo\EventBus\Implementation\EventSubscribersResolver;
use Damejidlo\EventBus\Implementation\InMemoryEventQueue;
use Damejidlo\EventBus\Implementation\SynchronousSubscriberSpecificDomainEventHandler;
use Damejidlo\EventBus\Middleware\SubscribersResolvingMiddleware;
use Damejidlo\EventBus\SynchronousEventDispatcher;
use Damejidlo\MessageBus\Middleware\GuardAgainstNestedHandlingMiddleware;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use Damejidlo\MessageBus\Middleware\LoggingMiddleware;
use Damejidlo\MessageBus\MiddlewareCallbackChainCreator;
use Damejidlo\MessageBus\MiddlewareSupportingMessageBus;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fakes\FakeCommandHandlerProvider;
use DamejidloTests\Integration\Fakes\FakeEventSubscriberProvider;
use DamejidloTests\Integration\Fixtures\CreateInvoiceOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\NotifyUserOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\OrderPlacedEvent;
use DamejidloTests\Integration\Fixtures\PlaceOrderCommand;
use DamejidloTests\Integration\Fixtures\PlaceOrderHandler;
use Psr\Log\Test\TestLogger;
use Tester\Assert;



class IntegrationTest extends DjTestCase
{

	/**
	 * @var TestLogger
	 */
	private $logger;

	/**
	 * @var ICommandBus
	 */
	private $commandBus;



	public function testWorkflow() : void
	{
		$command = new PlaceOrderCommand();
		$result = $this->commandBus->handle($command);
		// satisfy phpstan
		assert($result instanceof NewEntityId);
		Assert::same(1, $result->toInteger());

		Assert::equal(
			[
				[
					'level' => 'info',
					'message' => 'Command handling started.',
					'context' => [
						'commandType' => 'DamejidloTests\\Integration\\Fixtures\\PlaceOrderCommand',
					],
				],
				[
					'level' => 'info',
					'message' => 'Command handling ended successfully.',
					'context' => [
						'commandType' => 'DamejidloTests\\Integration\\Fixtures\\PlaceOrderCommand',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling started.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber started.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'subscriberType' => 'DamejidloTests\\Integration\\Fixtures\\CreateInvoiceOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber ended successfully.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'subscriberType' => 'DamejidloTests\\Integration\\Fixtures\\CreateInvoiceOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber started.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'subscriberType' => 'DamejidloTests\\Integration\\Fixtures\\NotifyUserOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber ended successfully.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'subscriberType' => 'DamejidloTests\\Integration\\Fixtures\\NotifyUserOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling ended successfully.',
					'context' => [
						'eventType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
					],
				],
			],
			$this->logger->records
		);
	}



	protected function setup() : void
	{
		// logger

		$logger = new TestLogger();
		$this->logger = $logger;

		// event bus

		$eventSubscribers = [
			new CreateInvoiceOnOrderPlaced(),
			new NotifyUserOnOrderPlaced(),
		];

		$eventSubscribersByEventType = [
			OrderPlacedEvent::class => $eventSubscribers,
		];

		$eventSubscribersResolver = new EventSubscribersResolver();
		foreach ($eventSubscribersByEventType as $eventType => $subscribers) {
			foreach ($subscribers as $subscriber) {
				$eventSubscribersResolver->registerSubscriber($eventType, get_class($subscriber));
			}
		}

		$eventSubscriberProvider = new FakeEventSubscriberProvider($eventSubscribers);

		$subscriberSpecificDomainEventHandler = new SynchronousSubscriberSpecificDomainEventHandler(
			$eventSubscriberProvider, new MiddlewareCallbackChainCreator()
		);
		$subscriberSpecificDomainEventHandler->appendMiddleware(new LoggingMiddleware($logger));

		$messageBus = new MiddlewareSupportingMessageBus();
		$messageBus->appendMiddleware(new LoggingMiddleware($logger));
		$messageBus->appendMiddleware(
			new SubscribersResolvingMiddleware($eventSubscribersResolver, $subscriberSpecificDomainEventHandler)
		);

		$eventBus = new EventBus($messageBus);

		// event dispatcher

		$isCurrentlyHandlingAwareMiddleware = new IsCurrentlyHandlingAwareMiddleware();
		$eventQueue = new InMemoryEventQueue();

		$synchronousEventDispatcher = new SynchronousEventDispatcher($eventBus);
		$commandBusAwareEventDispatcher = new CommandBusAwareEventDispatcher(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$synchronousEventDispatcher
		);

		// command bus

		$commandHandler = new PlaceOrderHandler($commandBusAwareEventDispatcher);
		$commandHandlersByCommandType = [
			PlaceOrderCommand::class => $commandHandler,
		];

		$commandHandlerResolver = new CommandHandlerResolver();
		foreach ($commandHandlersByCommandType as $commandType => $commandHandler) {
			$commandHandlerResolver->registerHandler($commandType, get_class($commandHandler));
		}

		$commandHandlerProvider = new FakeCommandHandlerProvider($commandHandlersByCommandType);

		$middleware = [
			new EventDispatchingMiddleware($eventQueue, $commandBusAwareEventDispatcher),
			new LoggingMiddleware($logger),
			new GuardAgainstNestedHandlingMiddleware($isCurrentlyHandlingAwareMiddleware),
			$isCurrentlyHandlingAwareMiddleware,
			new HandlerResolvingMiddleware($commandHandlerResolver, $commandHandlerProvider),
		];

		$messageBus = new MiddlewareSupportingMessageBus();
		foreach ($middleware as $oneMiddleware) {
			$messageBus->appendMiddleware($oneMiddleware);
		}

		$this->commandBus = new CommandBus($messageBus);
	}

}



(new IntegrationTest())->run();
