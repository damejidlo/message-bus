<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class MethodReturnTypeIsInRule
{

	/**
	 * @var string[]
	 */
	private $returnTypes;



	public function __construct(string ...$returnTypes)
	{
		$this->returnTypes = $returnTypes;
	}



	/**
	 * @param \ReflectionMethod $method
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(\ReflectionMethod $method) : void
	{
		$returnType = $method->getReturnType();

		if ($returnType === NULL) {
			$this->fail($method);
			return;
		}

		$returnTypeName = $returnType->getName();

		foreach ($this->returnTypes as $supportedReturnType) {
			if ($returnTypeName === $supportedReturnType || is_subclass_of($returnTypeName, $supportedReturnType)) {
				return;
			}
		}

		$this->fail($method);
	}



	/**
	 * @param \ReflectionMethod $method
	 * @throws StaticAnalysisFailedException
	 */
	private function fail(\ReflectionMethod $method) : void
	{
		throw StaticAnalysisFailedException::with(
			sprintf('Method "%s" return type must be "%s"', $method->getName(), implode('|', $this->returnTypes)),
			$method->getDeclaringClass()->getName()
		);
	}

}
