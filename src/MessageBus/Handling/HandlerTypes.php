<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\Middleware\MiddlewareContext;



final class HandlerTypes implements ITransferableInContext
{

	private const CONTEXT_KEY = 'handlerTypes';

	/**
	 * @var HandlerType[]
	 */
	private $types;



	private function __construct(HandlerType ...$types)
	{
		$this->types = $types;
	}



	public static function fromArrayOfStrings(string ...$types) : self
	{
		$types = array_map(function (string $type) : HandlerType {
			return HandlerType::fromString($type);
		}, $types);

		return new self(...$types);
	}



	public function count() : int
	{
		return count($this->types);
	}



	public function getOne() : HandlerType
	{
		if ($this->count() !== 1) {
			throw new \LogicException('Single handler type expected.');
		}

		return $this->types[0];
	}



	/**
	 * @return HandlerType[]
	 */
	public function toArray() : array
	{
		return $this->types;
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
