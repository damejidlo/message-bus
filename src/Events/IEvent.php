<?php
declare(strict_types = 1);

namespace Damejidlo\Events;

use Damejidlo\MessageBus\IMessage;

/**
 * Event implementation must adhere to these rules:
 * - class must be named <event-name>Event
 * - event name should be in past tense ("something happened")
 * - event must be a simple immutable DTO
 * - event must not contain entities, only references (i.e. "int $orderId", not "Order $order")
 *
 * Examples of good event class names:
 * - OrderRejectedEvent
 * - UserCreatedEvent
 */
interface IEvent extends IMessage
{

}
