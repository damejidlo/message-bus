<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Events\IEventSubscriber;
use Damejidlo\MessageBus\Handling\HandlerType;



final class MessageHandlerValidatorFactory
{

	public static function createDefault() : IMessageHandlerValidator
	{
		return new CompositeHandlerValidator(
			new ValidateOnlyWhenTypeMatchesHandlerValidator(
				HandlerType::fromString(ICommandHandler::class),
				new ConfigurableHandlerValidator(MessageHandlerValidationConfiguration::command())
			),
			new ValidateOnlyWhenTypeMatchesHandlerValidator(
				HandlerType::fromString(IEventSubscriber::class),
				new ConfigurableHandlerValidator(MessageHandlerValidationConfiguration::event())
			)
		);
	}

}
