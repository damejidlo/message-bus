<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class MethodReturnTypeIsSetRule
{

	/**
	 * @param \ReflectionMethod $method
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(\ReflectionMethod $method) : void
	{
		$returnType = $method->getReturnType();

		if ($returnType === NULL) {
			throw StaticAnalysisFailedException::with(
				sprintf('Method "%s" must have an explicit return type', $method->getName()),
				$method->getDeclaringClass()->getName()
			);
		}
	}

}
