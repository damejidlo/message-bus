<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class ClassIsFinalRule
{

	/**
	 * @param string $type
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $type) : void
	{
		$reflection = ReflectionHelper::requireClassReflection($type);

		if (!$reflection->isFinal()) {
			throw StaticAnalysisFailedException::with('Class must be final.', $type);
		}
	}

}
