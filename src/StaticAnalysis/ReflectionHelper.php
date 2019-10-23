<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use ReflectionClass;



final class ReflectionHelper
{

	/**
	 * @param string $type
	 * @return ReflectionClass
	 * @throws StaticAnalysisFailedException
	 */
	public static function requireClassReflection(string $type) : ReflectionClass
	{
		try {
			return new ReflectionClass($type);

		} catch (\ReflectionException $exception) {
			throw StaticAnalysisFailedException::with('Class does not exist', $type);
		}
	}

}
