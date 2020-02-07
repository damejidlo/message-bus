<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassNameHasSuffixRule;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class MessageType
{

	/**
	 * @var string
	 */
	private $type;



	private function __construct(string $type)
	{
		$this->type = $type;
	}



	public static function fromMessage(IMessage $message) : self
	{
		return new self(get_class($message));
	}



	public static function fromString(string $type) : self
	{
		return new self($type);
	}



	public function toString() : string
	{
		return $this->type;
	}



	public function getGeneralType() : string
	{
		if (is_subclass_of($this->type, ICommand::class)) {
			return 'Command';

		} elseif (is_subclass_of($this->type, IEvent::class)) {
			return 'Event';

		} else {
			return 'Message';
		}
	}



	public function isHandlerRequired() : bool
	{
		return ! is_subclass_of($this->type, IEvent::class);
	}



	/**
	 * @param string $suffix
	 * @return string message name without namespace and suffix
	 * @throws StaticAnalysisFailedException
	 */
	public function shortName(string $suffix) : string
	{
		$messageTypeReflection = ReflectionHelper::requireClassReflection($this->toString());

		$rule = new ClassNameHasSuffixRule($suffix);
		$rule->validate($this->toString());

		preg_match($rule->getRegexPattern(), $messageTypeReflection->getShortName(), $matches);

		return (string) $matches[1];
	}

}
