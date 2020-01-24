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
use Damejidlo\MessageBus\StaticAnalysis\MessageHandlerValidationConfiguration;
use Damejidlo\MessageBus\StaticAnalysis\MessageHandlerValidationConfigurations;
use Damejidlo\MessageBus\StaticAnalysis\MessageHandlerValidator;
use DamejidloTests\DjTestCase;
use DamejidloTests\Integration\Fixtures\GetOrderQuery;
use DamejidloTests\Integration\Fixtures\GetOrderQueryHandler;
use DamejidloTests\Integration\Fixtures\GetOrderQueryResult;
use DamejidloTests\Integration\Fixtures\IQuery;
use DamejidloTests\Integration\Fixtures\IQueryHandler;
use DamejidloTests\Integration\Fixtures\IQueryResult;
use Tester\Assert;



class QueryHandlingTest extends DjTestCase
{

	public function testValidation() : void
	{
		$configurations = MessageHandlerValidationConfigurations::fromArray([
			new MessageHandlerValidationConfiguration(
				HandlerType::fromString(IQueryHandler::class),
				TRUE,
				TRUE,
				'handle',
				'query',
				IQuery::class,
				[IQueryResult::class],
				'Query',
				'QueryHandler',
				''
			),
		]);

		$validator = new MessageHandlerValidator($configurations);

		Assert::noError(function () use ($validator) : void {
			$validator->validate(GetOrderQueryHandler::class);
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
