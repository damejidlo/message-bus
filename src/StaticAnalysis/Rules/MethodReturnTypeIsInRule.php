<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class MethodReturnTypeIsInRule
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
		if (! in_array($returnTypeName, $this->returnTypes, TRUE)) {
			$this->fail($method);
			return;
		}
	}



	/**
	 * @param \ReflectionMethod $method
	 * @throws StaticAnalysisFailedException
	 */
	private function fail(\ReflectionMethod $method) : void
	{
		throw StaticAnalysisFailedException::with(
			sprintf('Method "%s" return type must be in [%s]', $method->getName(), implode(', ', $this->returnTypes)),
			$method->getDeclaringClass()->getName()
		);
	}

}
