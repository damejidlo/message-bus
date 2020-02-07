<?php declare(strict_types = 1);

namespace DamejidloTests\Integration\Fixtures;

final class GetOrderQueryHandler implements IQueryHandler
{

	public function handle(GetOrderQuery $query) : GetOrderQueryResult
	{
		return new GetOrderQueryResult();
	}

}
