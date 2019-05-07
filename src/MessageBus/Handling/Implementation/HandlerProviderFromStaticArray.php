<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling\Implementation;

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\IHandlerProvider;
use Damejidlo\MessageBus\IMessageHandler;



final class HandlerProviderFromStaticArray implements IHandlerProvider
{

	/**
	 * @var IMessageHandler[]
	 */
	private $handlersByType;



	/**
	 * @param IMessageHandler[] $handlersByType
	 */
	public function __construct(array $handlersByType)
	{
		$this->handlersByType = $handlersByType;
	}



	public function get(HandlerType $type) : IMessageHandler
	{
		$typeAsString = $type->toString();

		if (! isset($this->handlersByType[$typeAsString])) {
			throw new \OutOfRangeException(sprintf('Handler of type "%s" could not be found.', $typeAsString));
		}

		return $this->handlersByType[$typeAsString];
	}

}
