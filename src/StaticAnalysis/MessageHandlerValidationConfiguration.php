<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Commands\ICommand;
use Damejidlo\MessageBus\Commands\NewEntityId;
use Damejidlo\MessageBus\Events\IEvent;
use Damejidlo\MessageBus\IMessage;



class MessageHandlerValidationConfiguration
{

	/**
	 * @var bool
	 */
	private $handlerClassMustBeFinal = TRUE;

	/**
	 * @var string
	 */
	private $handleMethodName = 'handle';

	/**
	 * @var string
	 */
	private $handleMethodParameterName = 'message';

	/**
	 * @var string
	 */
	private $handleMethodParameterType = IMessage::class;

	/**
	 * @var string[]
	 */
	private $handleMethodAllowedReturnTypes = ['void'];

	/**
	 * @var string
	 */
	private $messageClassSuffix = '';

	/**
	 * @var string
	 */
	private $handlerClassSuffix = '';

	/**
	 * @var string
	 */
	private $handlerClassPrefixRegex = '';



	private function __construct()
	{
	}



	public static function command() : self
	{
		$configuration = new self();

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
		$configuration = new self();

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



	public function handlerClassMustBeFinal() : bool
	{
		return $this->handlerClassMustBeFinal;
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
