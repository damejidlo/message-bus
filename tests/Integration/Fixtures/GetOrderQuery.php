<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

final class GetOrderQuery implements IQuery
{

	/**
	 * @var int
	 */
	private $id;



	public function __construct(int $id)
	{
		$this->id = $id;
	}



	public function getId() : int
	{
		return $this->id;
	}

}
