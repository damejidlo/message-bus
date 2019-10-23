<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Events;

use Damejidlo\MessageBus\IMessageHandler;



/**
 * Event subscriber implementation must adhere to these rules:
 * - class must be named <do-something>On<event-name>
 * - class must be final
 * - class must implement method named "handle"
 * - handle method must have exactly one parameter named "event"
 * - handle method parameter must be of type IEvent
 * - handle method return type must be "void"
 * - handle method must be annotated with "@throws" tags if specific exceptions can be thrown
 *
 * Example:
 * final class DoSomethingOnSomethingHappened implements IEventSubscriber {
 *      public function handle(SomethingHappenedEvent $event) : void {}
 * }
 */
interface IEventSubscriber extends IMessageHandler
{

}
