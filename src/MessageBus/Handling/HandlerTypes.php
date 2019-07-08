<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

final class HandlerTypes
{

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

}
