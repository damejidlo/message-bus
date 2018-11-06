<?php
declare(strict_types = 1);

namespace Damejidlo\CommandBus;

use Damejidlo\MessageBus\IBusMessageHandler;



/**
 * Command handler implementation must adhere to these rules:
 * - class must be named <command-name>Handler
 * - class must be final
 * - class must implement method named "handle"
 * - handle method must have exactly one parameter named "command"
 * - handle method parameter must be of type ICommand
 * - handle method return type must be "void" or non-nullable "NewEntityId" - @see NewEntityId
 * - handle method must be annotated with "@throws" tags if specific exceptions can be thrown
 *
 * Examples:
 *
 * class DoSomethingHandler {
 *      public function handle(DoSomethingCommand $command) : void {}
 * }
 *
 * class CreateSomeEntityHandler {
 *      public function handle(CreateSomeEntityCommand $command) : NewEntityId {}
 * }
 */
interface ICommandHandler extends IBusMessageHandler
{

}
