<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Events\CommandBusAwareEventDispatcher;
use Damejidlo\Events\InMemoryEventQueue;
use Damejidlo\Events\SynchronousEventDispatcher;
use Damejidlo\MessageBus\Commands\CommandBus;
use Damejidlo\MessageBus\Commands\ICommandBus;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerProvider;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\EventDispatchingMiddleware;
use Damejidlo\MessageBus\Middleware\GuardAgainstNestedHandlingMiddleware;
use Damejidlo\MessageBus\Middleware\HandlerInvokingMiddleware;
use Damejidlo\MessageBus\Middleware\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Middleware\IsCurrentlyHandlingAwareMiddleware;
use Damejidlo\MessageBus\Middleware\LoggingMiddleware;
use Damejidlo\MessageBus\Middleware\SplitByHandlerTypeMiddleware;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\CreateInvoiceOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\NotifyUserOnOrderPlaced;
use DamejidloTests\Integration\Fixtures\OrderPlacedEvent;
use DamejidloTests\Integration\Fixtures\PlaceOrderCommand;
use DamejidloTests\Integration\Fixtures\PlaceOrderHandler;
use Psr\Log\Test\TestLogger;
use Tester\Assert;



class SynchronousEventsDispatchingTest extends DjTestCase
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
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\PlaceOrderCommand',
					],
				],
				[
					'level' => 'info',
					'message' => 'Command handling ended successfully.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\PlaceOrderCommand',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling started.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber started.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'handlerType' => 'DamejidloTests\\Integration\\Fixtures\\CreateInvoiceOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber ended successfully.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'handlerType' => 'DamejidloTests\\Integration\\Fixtures\\CreateInvoiceOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber started.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'handlerType' => 'DamejidloTests\\Integration\\Fixtures\\NotifyUserOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling in subscriber ended successfully.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
						'handlerType' => 'DamejidloTests\\Integration\\Fixtures\\NotifyUserOnOrderPlaced',
					],
				],
				[
					'level' => 'info',
					'message' => 'Event handling ended successfully.',
					'context' => [
						'messageType' => 'DamejidloTests\\Integration\\Fixtures\\OrderPlacedEvent',
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

		$subscriberTypesResolver = new ArrayMapHandlerTypesResolver([
			OrderPlacedEvent::class => [
				CreateInvoiceOnOrderPlaced::class,
				NotifyUserOnOrderPlaced::class,
			],
		]);

		$subscriberProvider = new ArrayMapHandlerProvider([
			CreateInvoiceOnOrderPlaced::class => new CreateInvoiceOnOrderPlaced(),
			NotifyUserOnOrderPlaced::class => new NotifyUserOnOrderPlaced(),
		]);

		$subscriberInvoker = new HandlerInvoker();

		$middleware = [
			new HandlerTypesResolvingMiddleware($subscriberTypesResolver),
			new LoggingMiddleware($logger),
			new SplitByHandlerTypeMiddleware(),
			new LoggingMiddleware($logger),
			new HandlerInvokingMiddleware($subscriberProvider, $subscriberInvoker),
		];

		$messageBus = new MessageBus($middleware);

		// event dispatcher

		$isCurrentlyHandlingAwareMiddleware = new IsCurrentlyHandlingAwareMiddleware();
		$eventQueue = new InMemoryEventQueue();

		$synchronousEventDispatcher = new SynchronousEventDispatcher($messageBus);
		$commandBusAwareEventDispatcher = new CommandBusAwareEventDispatcher(
			$isCurrentlyHandlingAwareMiddleware,
			$eventQueue,
			$synchronousEventDispatcher
		);

		// command bus

		$commandHandler = new PlaceOrderHandler($commandBusAwareEventDispatcher);

		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			PlaceOrderCommand::class => [
				PlaceOrderHandler::class,
			],
		]);

		$handlerProvider = new ArrayMapHandlerProvider([
			PlaceOrderHandler::class => $commandHandler,
		]);

		$handlerInvoker = new HandlerInvoker();

		$middleware = [
			new EventDispatchingMiddleware($eventQueue, $commandBusAwareEventDispatcher),
			new LoggingMiddleware($logger),
			new GuardAgainstNestedHandlingMiddleware($isCurrentlyHandlingAwareMiddleware),
			$isCurrentlyHandlingAwareMiddleware,
			new HandlerTypesResolvingMiddleware($handlerTypesResolver),
			new SplitByHandlerTypeMiddleware(),
			new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker),
		];

		$messageBus = new MessageBus($middleware);

		$this->commandBus = new CommandBus($messageBus);
	}

}



(new SynchronousEventsDispatchingTest())->run();
