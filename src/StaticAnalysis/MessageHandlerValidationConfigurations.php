<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\HandlerType;



final class MessageHandlerValidationConfigurations
{

	/**
	 * @var MessageHandlerValidationConfiguration[]
	 */
	private $configurations;



	private function __construct(MessageHandlerValidationConfiguration ...$configurations)
	{
		$this->configurations = $configurations;
	}



	/**
	 * @param MessageHandlerValidationConfiguration[] $configurations
	 * @return static
	 */
	public static function fromArray(array $configurations) : self
	{
		return new self(...$configurations);
	}



	public static function default() : self
	{
		return new self(
			MessageHandlerValidationConfiguration::command(),
			MessageHandlerValidationConfiguration::event()
		);
	}



	public function get(HandlerType $handlerType) : MessageHandlerValidationConfiguration
	{
		foreach ($this->configurations as $configuration) {
			if ($configuration->supports($handlerType)) {
				return $configuration;
			}
		}

		throw new \LogicException(sprintf('Handler validation not found, unsupported handler class: "%s".', $handlerType->toString()));
	}

}
