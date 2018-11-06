<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus\Implementation;

use Nette\SmartObject;
use Nette\Utils\Validators;



class NewEntityId
{

	use SmartObject;

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
		if (!Validators::isNumericInt($this->value)) {
			throw new \LogicException(sprintf('New entity id value "%s" cannot be converted to integer.', $this->value));
		}

		return (int) $this->value;
	}

}
