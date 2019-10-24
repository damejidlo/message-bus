<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class MethodReturnTypeIsNotNullableRule
{

	/**
	 * @param \ReflectionMethod $method
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(\ReflectionMethod $method) : void
	{
		$returnType = $method->getReturnType();

		if ($returnType !== NULL && $returnType->allowsNull()) {
			throw StaticAnalysisFailedException::with(
				sprintf('Method "%s" return type must not be nullable', $method->getName()),
				$method->getDeclaringClass()->getName()
			);
		}
	}

}
