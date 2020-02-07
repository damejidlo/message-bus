<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;



interface IMessageHandlerValidator
{

	/**
	 * @param HandlerType $handlerType
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(HandlerType $handlerType) : void;

}
