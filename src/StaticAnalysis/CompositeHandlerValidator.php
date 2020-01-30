<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;



final class CompositeHandlerValidator implements IMessageHandlerValidator
{

	/**
	 * @var IMessageHandlerValidator[]
	 */
	private $validators;



	public function __construct(IMessageHandlerValidator ...$validators)
	{
		$this->validators = $validators;
	}



	public function validate(HandlerType $handlerType) : void
	{
		foreach ($this->validators as $validator) {
			$validator->validate($handlerType);
		}
	}

}
