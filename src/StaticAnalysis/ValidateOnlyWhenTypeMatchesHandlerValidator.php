<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassExistsRule;



final class ValidateOnlyWhenTypeMatchesHandlerValidator implements IMessageHandlerValidator
{

	/**
	 * @var HandlerType
	 */
	private $type;

	/**
	 * @var IMessageHandlerValidator
	 */
	private $validator;



	public function __construct(HandlerType $type, IMessageHandlerValidator $validator)
	{
		$this->type = $type;
		$this->validator = $validator;
	}



	public static function create(HandlerType $type, IMessageHandlerValidator $validator) : self
	{
		return new self($type, $validator);
	}



	public function validate(HandlerType $type) : void
	{
		(new ClassExistsRule())->validate($type->toString());

		if ($type->isSubtypeOf($this->type)) {
			$this->validator->validate($type);
		}
	}

}
