<?php declare(strict_types = 1);

namespace Damejidlo\CommandBus;

use Damejidlo\CommandBus\Implementation\NewEntityId;
use Damejidlo\MessageBus\IMessageBus;



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
		return $this->delegate->handle($command);
	}

}
