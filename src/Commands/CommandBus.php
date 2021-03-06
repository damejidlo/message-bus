<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Commands;

use Damejidlo\MessageBus\IMessageBus;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class CommandBus implements ICommandBus
{

	/**
	 * @var IMessageBus
	 */
	private $delegate;



	public function __construct(IMessageBus $delegate)
	{
		$this->delegate = $delegate;
	}



	public function handle(ICommand $command) : ?NewEntityId
	{
		return $this->delegate->handle($command, MiddlewareContext::empty());
	}

}
