<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Middleware;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\CommandBus\ICommandHandlerProvider;
use Damejidlo\CommandBus\ICommandHandlerResolver;
use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\IMessageBusMiddleware;
use Damejidlo\MessageBus\Middleware\MiddlewareCallback;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class HandlerResolvingMiddleware implements IMessageBusMiddleware
{

	/**
	 * @var ICommandHandlerResolver
	 */
	private $commandHandlerResolver;

	/**
	 * @var ICommandHandlerProvider
	 */
	private $commandHandlerProvider;



	public function __construct(
		ICommandHandlerResolver $commandHandlerResolver,
		ICommandHandlerProvider $commandHandlerProvider
	) {
		$this->commandHandlerResolver = $commandHandlerResolver;
		$this->commandHandlerProvider = $commandHandlerProvider;
	}



	/**
	 * @inheritdoc
	 */
	public function handle(IBusMessage $message, MiddlewareContext $context, MiddlewareCallback $nextMiddlewareCallback)
	{
		$command = $this->castMessageToCommand($message);

		$handlerType = $this->commandHandlerResolver->resolve($command);
		$handler = $this->commandHandlerProvider->getByType($handlerType);

		$handleMethod = 'handle';
		$callback = [$handler, $handleMethod];

		if (!is_callable($callback)) {
			throw new \LogicException(
				sprintf('Method "%s" of handler "%s" is not callable.', $handleMethod, $handlerType)
			);
		}

		return call_user_func($callback, $command);
	}



	private function castMessageToCommand(IBusMessage $message) : ICommand
	{
		if (!$message instanceof ICommand) {
			throw new \InvalidArgumentException(sprintf('ICommand instance expected, %s given.', get_class($message)));
		}

		return $message;
	}

}
