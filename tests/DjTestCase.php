<?php
declare(strict_types = 1);

namespace DamejidloTests;

use Mockery;
use Tester\TestCase;



class DjTestCase extends TestCase
{

	protected function tearDown() : void
	{
		parent::tearDown();
		Mockery::close();
	}

}
