<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;
use ReflectionParameter;



final class MethodParameterTypeMatchesRule
{

	/**
	 * @var string
	 */
	private $parameterType;



	public function __construct(string $parameterType)
	{
		$this->parameterType = $parameterType;
	}



	/**
	 * @param ReflectionParameter $parameter
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(ReflectionParameter $parameter) : void
	{
		if ($parameter->getType() === NULL || !is_subclass_of($parameter->getType()->getName(), $this->parameterType)) {
			$class = $parameter->getDeclaringClass();
			if ($class === NULL) {
				throw new \LogicException('Class must be set in this context.');
			}

			throw StaticAnalysisFailedException::with(
				sprintf('Method parameter "%s" must be of type "%s"', $parameter->getName(), $this->parameterType),
				$class->getName()
			);
		}
	}

}
