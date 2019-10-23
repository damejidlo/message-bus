<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class ClassExistsRule
{

	/**
	 * @param string $type
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $type) : void
	{
		ReflectionHelper::requireClassReflection($type);
	}

}
