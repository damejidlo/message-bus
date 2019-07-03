<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\IMessageHandler;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class HandlerType implements ITransferableInContext
{

	public const CONTEXT_KEY = 'handlerType';

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



	public function saveTo(MiddlewareContext $context) : MiddlewareContext
	{
		return $context->with(self::CONTEXT_KEY, $this);
	}



	public static function extractFrom(MiddlewareContext $context) : self
	{
		return $context->get(self::CONTEXT_KEY);
	}

}
