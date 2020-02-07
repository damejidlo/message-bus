<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class ClassHasPublicMethodRule
{

	/**
	 * @var string
	 */
	private $methodName;



	public function __construct(string $methodName)
	{
		$this->methodName = $methodName;
	}



	/**
	 * @param string $type
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $type) : void
	{
		$method = ReflectionHelper::requireMethodReflection($type, $this->methodName);

		if (! $method->isPublic()) {
			throw StaticAnalysisFailedException::with(sprintf('Method "%s" is not public', $this->methodName), $type);
		}
	}

}
