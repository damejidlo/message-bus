<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;
use ReflectionParameter;



class MethodParameterNameMatchesRule
{

	/**
	 * @var string
	 */
	private $parameterName;



	public function __construct(string $parameterName)
	{
		$this->parameterName = $parameterName;
	}



	/**
	 * @param ReflectionParameter $parameter
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(ReflectionParameter $parameter) : void
	{
		if ($parameter->getName() !== $this->parameterName) {
			$class = $parameter->getDeclaringClass();
			if ($class === NULL) {
				throw new \LogicException('Class must be set in this context.');
			}

			throw StaticAnalysisFailedException::with(
				sprintf('Method parameter name must be "%s"', $this->parameterName),
				$class->getName()
			);
		}
	}

}
