<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\CommandBus\ICommand;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\MessageBus\IMessage;



final class MessageType
{

	/**
	 * @var string
	 */
	private $type;



	private function __construct(string $type)
	{
		$this->type = $type;
	}



	public static function fromMessage(IMessage $message) : self
	{
		return new self(get_class($message));
	}



	public function toString() : string
	{
		return $this->type;
	}



	public function toGeneralType() : string
	{
		if (is_subclass_of($this->type, ICommand::class)) {
			return 'Command';

		} elseif (is_subclass_of($this->type, IDomainEvent::class)) {
			return 'Event';

		} else {
			return 'Message';
		}
	}

}
