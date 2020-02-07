<?php

namespace DamejidloTests\MessageBus\StaticAnalysis\Events\Fixtures;

use Damejidlo\MessageBus\Events\IEventSubscriber;



final class HandleMethodHasNullReturnTypeOnSomethingValidHappened implements IEventSubscriber
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @param SomethingValidHappenedEvent $event
	 */
	public function handle(SomethingValidHappenedEvent $event)
	{
	}

}
