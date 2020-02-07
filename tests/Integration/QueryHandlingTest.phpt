<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\Integration;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerProvider;
use Damejidlo\MessageBus\Handling\Implementation\ArrayMapHandlerTypesResolver;
use Damejidlo\MessageBus\Handling\Implementation\HandlerInvoker;
use Damejidlo\MessageBus\MessageBus;
use Damejidlo\MessageBus\Middleware\HandlerInvokingMiddleware;
use Damejidlo\MessageBus\Middleware\HandlerTypesResolvingMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;
use Damejidlo\MessageBus\Middleware\SplitByHandlerTypeMiddleware;
use Damejidlo\MessageBus\StaticAnalysis\ConfigurableHandlerValidator;
use Damejidlo\MessageBus\StaticAnalysis\MessageHandlerValidationConfiguration;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\GetOrderQuery;
use DamejidloTests\Integration\Fixtures\GetOrderQueryHandler;
use DamejidloTests\Integration\Fixtures\GetOrderQueryResult;
use DamejidloTests\Integration\Fixtures\IQuery;
use DamejidloTests\Integration\Fixtures\IQueryResult;
use Tester\Assert;



class QueryHandlingTest extends DjTestCase
{

	public function testValidation() : void
	{
		$configuration = new MessageHandlerValidationConfiguration(
			TRUE,
			TRUE,
			'handle',
			'query',
			IQuery::class,
			[IQueryResult::class],
			'Query',
			'QueryHandler',
			''
		);

		$validator = new ConfigurableHandlerValidator($configuration);

		Assert::noError(function () use ($validator) : void {
			$validator->validate(HandlerType::fromString(GetOrderQueryHandler::class));
		});
	}



	public function testHandleSucceeds() : void
	{
		$handler = new GetOrderQueryHandler();

		$handlerTypesResolver = new ArrayMapHandlerTypesResolver([
			GetOrderQuery::class => [
				GetOrderQueryHandler::class,
			],
		]);

		$handlerProvider = new ArrayMapHandlerProvider([
			GetOrderQueryHandler::class => $handler,
		]);

		$handlerInvoker = new HandlerInvoker();

		$bus = new MessageBus(
			new HandlerTypesResolvingMiddleware($handlerTypesResolver),
			new SplitByHandlerTypeMiddleware(),
			new HandlerInvokingMiddleware($handlerProvider, $handlerInvoker)
		);

		$command = new GetOrderQuery(1);
		$result = $bus->handle($command, MiddlewareContext::empty());

		Assert::type(GetOrderQueryResult::class, $result);
	}

}



(new QueryHandlingTest())->run();
