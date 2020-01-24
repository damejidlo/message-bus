<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessageHandler;



final class HandlerType
{

	/**
	 * @var string
	 */
	private $type;



	private function __construct(string $type)
	{
		$this->type = $type;
	}



	public static function fromHandler(IMessageHandler $handler) : self
	{
		return new self(get_class($handler));
	}



	public static function fromString(string $type) : self
	{
		return new self($type);
	}



	public function toString() : string
	{
		return $this->type;
	}



	public function isSubtypeOf(HandlerType $handlerType) : bool
	{
		return is_subclass_of($this->type, $handlerType->toString());
	}

}
