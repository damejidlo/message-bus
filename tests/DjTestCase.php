<?php
declare(strict_types = 1);

namespace DamejidloTests;

use Mockery;
use Tester\TestCase;



class DjTestCase extends TestCase
{

	public function run() : void
	{
		if (getenv('IS_PHPSTAN') === '1') {
			return;
		}
		parent::run();
	}



	protected function tearDown() : void
	{
		parent::tearDown();
		Mockery::close();
	}

}
