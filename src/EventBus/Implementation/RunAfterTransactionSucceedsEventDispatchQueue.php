<?php
declare(strict_types = 1);

namespace Damejidlo\EventBus\Implementation;

use Damejidlo\Doctrine\TransactionListener;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventBus;
use Damejidlo\EventBus\IEventDispatchQueue;
use Nette\SmartObject;



class RunAfterTransactionSucceedsEventDispatchQueue implements IEventDispatchQueue
{

	use SmartObject;

	/**
	 * @var IEventBus
	 */
	private $eventBus;

	/**
	 * @var TransactionListener
	 */
	protected $transactionListener;



	/**
	 * @param IEventBus $eventBus
	 * @param TransactionListener $transactionListener
	 */
	public function __construct(
		IEventBus $eventBus,
		TransactionListener $transactionListener
	) {
		$this->eventBus = $eventBus;
		$this->transactionListener = $transactionListener;
	}



	public function enqueue(IDomainEvent $event) : void
	{
		$this->transactionListener->runAfterTransactionSucceeds(function () use ($event) : void {
			$this->eventBus->handle($event);
		});
	}

}
