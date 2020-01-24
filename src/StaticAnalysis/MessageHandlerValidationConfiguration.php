<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\ICommandHandler;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\Events\IEventSubscriber;
use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\IMessage;



class MessageHandlerValidationConfiguration
{

	/**
	 * @var HandlerType
	 */
	private $supportedHandlerType;

	/**
	 * @var bool
	 */
	private $handlerClassMustBeFinal;

	/**
	 * @var bool
	 */
	private $messageClassMustBeFinal;

	/**
	 * @var string
	 */
	private $handleMethodName;

	/**
	 * @var string
	 */
	private $handleMethodParameterName;

	/**
	 * @var string
	 */
	private $handleMethodParameterType;

	/**
	 * @var string[]
	 */
	private $handleMethodAllowedReturnTypes;

	/**
	 * @var string
	 */
	private $messageClassSuffix;

	/**
	 * @var string
	 */
	private $handlerClassSuffix;

	/**
	 * @var string
	 */
	private $handlerClassPrefixRegex;



	/**
	 * @param HandlerType $supportedHandlerType
	 * @param bool $handlerClassMustBeFinal
	 * @param bool $messageClassMustBeFinal
	 * @param string $handleMethodName
	 * @param string $handleMethodParameterName
	 * @param string $handleMethodParameterType
	 * @param string[] $handleMethodAllowedReturnTypes
	 * @param string $messageClassSuffix
	 * @param string $handlerClassSuffix
	 * @param string $handlerClassPrefixRegex
	 */
	public function __construct(
		HandlerType $supportedHandlerType,
		bool $handlerClassMustBeFinal = TRUE,
		bool $messageClassMustBeFinal = TRUE,
		string $handleMethodName = 'handle',
		string $handleMethodParameterName = 'message',
		string $handleMethodParameterType = IMessage::class,
		array $handleMethodAllowedReturnTypes = ['void'],
		string $messageClassSuffix = '',
		string $handlerClassSuffix = '',
		string $handlerClassPrefixRegex = ''
	) {
		$this->supportedHandlerType = $supportedHandlerType;
		$this->handlerClassMustBeFinal = $handlerClassMustBeFinal;
		$this->messageClassMustBeFinal = $messageClassMustBeFinal;
		$this->handleMethodName = $handleMethodName;
		$this->handleMethodParameterName = $handleMethodParameterName;
		$this->handleMethodParameterType = $handleMethodParameterType;
		$this->handleMethodAllowedReturnTypes = $handleMethodAllowedReturnTypes;
		$this->messageClassSuffix = $messageClassSuffix;
		$this->handlerClassSuffix = $handlerClassSuffix;
		$this->handlerClassPrefixRegex = $handlerClassPrefixRegex;
	}



	public static function command() : self
	{
		$configuration = new self(HandlerType::fromString(ICommandHandler::class));

		$configuration->handleMethodParameterName = 'command';
		$configuration->handleMethodParameterType = ICommand::class;
		$configuration->handleMethodAllowedReturnTypes = [
			'void',
			NewEntityId::class,
		];

		$configuration->messageClassSuffix = 'Command';
		$configuration->handlerClassSuffix = 'Handler';
		$configuration->handlerClassPrefixRegex = '';

		return $configuration;
	}



	public static function event() : self
	{
		$configuration = new self(HandlerType::fromString(IEventSubscriber::class));

		$configuration->handleMethodParameterName = 'event';
		$configuration->handleMethodParameterType = IEvent::class;
		$configuration->handleMethodAllowedReturnTypes = [
			'void',
		];

		$configuration->messageClassSuffix = 'Event';
		$configuration->handlerClassSuffix = '';
		$configuration->handlerClassPrefixRegex = '(.+)On';

		return $configuration;
	}



	public function supports(HandlerType $handlerType) : bool
	{
		return $handlerType->isSubtypeOf($this->supportedHandlerType);
	}



	public function handlerClassMustBeFinal() : bool
	{
		return $this->handlerClassMustBeFinal;
	}



	public function messageClassMustBeFinal() : bool
	{
		return $this->messageClassMustBeFinal;
	}



	public function handleMethodName() : string
	{
		return $this->handleMethodName;
	}



	public function handleMethodParameterName() : string
	{
		return $this->handleMethodParameterName;
	}



	public function getHandleMethodParameterType() : string
	{
		return $this->handleMethodParameterType;
	}



	/**
	 * @return string[]
	 */
	public function handleMethodAllowedReturnTypes() : array
	{
		return $this->handleMethodAllowedReturnTypes;
	}



	public function messageClassSuffix() : string
	{
		return $this->messageClassSuffix;
	}



	public function handlerClassSuffix() : string
	{
		return $this->handlerClassSuffix;
	}



	public function handlerClassPrefixRegex() : string
	{
		return $this->handlerClassPrefixRegex;
	}

}
