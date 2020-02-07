<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use ReflectionClass;
use ReflectionMethod;



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



	/**
	 * @param string $class
	 * @param string $methodName
	 * @return ReflectionMethod
	 * @throws StaticAnalysisFailedException
	 */
	public static function requireMethodReflection(string $class, string $methodName) : ReflectionMethod
	{
		try {
			return self::requireClassReflection($class)->getMethod($methodName);

		} catch (\ReflectionException $exception) {
			throw StaticAnalysisFailedException::with(sprintf('Method "%s" does not exist', $methodName), $class);
		}
	}

}
