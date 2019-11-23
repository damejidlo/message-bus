<?php declare(strict_types = 1);

namespace DamejidloTests\MessageBus\Fakes;

class MiddlewareLog
{

	/**
	 * @var RecordingMiddleware[]
	 */
	public $middlewareCalled = [];

}
