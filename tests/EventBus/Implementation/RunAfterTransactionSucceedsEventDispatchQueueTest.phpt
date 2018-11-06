<?php
declare(strict_types = 1);

/**
 * @testCase
 */

namespace DamejidloTests\EventBus\Implementation;

require_once __DIR__ . '/../../../bootstrap.php';

use Damejidlo\Doctrine\TransactionListener;
use Damejidlo\EventBus\IDomainEvent;
use Damejidlo\EventBus\IEventBus;
use Damejidlo\EventBus\Implementation\RunAfterTransactionSucceedsEventDispatchQueue;
use DamejidloTests\DjTestCase;
use Mockery;
use Mockery\MockInterface;
use Tester\Assert;



class RunAfterTransactionSucceedsEventDispatchQueueTest extends DjTestCase
{

	public function testCollectWhenCommandBusIsNotHandling() : void
	{
		$eventBus = $this->mockEventBus();
		$transactionListener = $this->mockTransactionListener();

		$queue = new RunAfterTransactionSucceedsEventDispatchQueue($eventBus, $transactionListener);

		$event = $this->mockEvent();

		// expectations
		$eventBus->shouldReceive('handle')->once()->withArgs([$event]);
		$transactionListener->shouldReceive('runAfterTransactionSucceeds')->withArgs(function (\Closure $closure) : bool {
			$closure();
			return TRUE;
		});

		Assert::noError(function () use ($queue, $event) : void {
			$queue->enqueue($event);
		});
	}



	/**
	 * @return IEventBus|MockInterface
	 */
	private function mockEventBus() : IEventBus
	{
		$mock = Mockery::mock(IEventBus::class);

		return $mock;
	}



	/**
	 * @return TransactionListener|MockInterface
	 */
	private function mockTransactionListener() : TransactionListener
	{
		$mock = Mockery::mock(TransactionListener::class);

		return $mock;
	}



	/**
	 * @return IDomainEvent|MockInterface
	 */
	private function mockEvent() : IDomainEvent
	{
		$mock = Mockery::mock(IDomainEvent::class);

		return $mock;
	}

}

(new RunAfterTransactionSucceedsEventDispatchQueueTest())->run();
