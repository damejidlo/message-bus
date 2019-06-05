<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Handling;

use Damejidlo\MessageBus\Middleware\MiddlewareContext;



interface ITransferableInContext
{

	public function saveTo(MiddlewareContext $context) : MiddlewareContext;



	/**
	 * @param MiddlewareContext $context
	 *
	 * // in PHP 7.4 return type can be changed to `self`
	 * @return mixed
	 */
	public static function extractFrom(MiddlewareContext $context);

}
