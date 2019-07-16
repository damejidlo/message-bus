<?php
declare(strict_types = 1);

namespace Damejidlo\Commands;

class NewEntityId
{

	/**
	 * @var string
	 */
	private $value;



	public function __construct(string $value)
	{
		$this->value = $value;
	}



	public static function fromInteger(int $value) : self
	{
		return new static((string) $value);
	}



	public function getValue() : string
	{
		return $this->value;
	}



	public function toInteger() : int
	{
		if (!$this->isNumericInt($this->value)) {
			throw new \LogicException(sprintf('New entity id value "%s" cannot be converted to integer.', $this->value));
		}

		return (int) $this->value;
	}



	public static function isNumericInt(string $value) : bool
	{
		return preg_match('#^-?[0-9]+\z#', $value) === 1;
	}

}
