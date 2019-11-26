<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

use Damejidlo\MessageBus\IMessage;



final class MiddlewareCallback
{

	/**
	 * @var \Closure
	 */
	private $callback;



	private function __construct(\Closure $callback)
	{
		$this->callback = $callback;
	}



	public static function fromClosure(\Closure $callback) : self
	{
		return new self($callback);
	}



	public static function empty() : self
	{
		return new self(
			function (IMessage $message) : void {
			}
		);
	}



	/**
	 * @param IMessage $message
	 * @param MiddlewareContext $context
	 * @return mixed
	 */
	public function __invoke(IMessage $message, MiddlewareContext $context)
	{
		return ($this->callback)($message, $context);
	}

}
