<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



final class ShortClassNameMatchesRule
{

	/**
	 * @var string
	 */
	private $regexPattern;



	public function __construct(string $regexPattern)
	{
		$this->regexPattern = $regexPattern;
	}



	/**
	 * @param string $type
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $type) : void
	{
		$typeReflection = ReflectionHelper::requireClassReflection($type);

		$matches = [];
		if (! preg_match($this->regexPattern, $typeReflection->getShortName(), $matches)) {
			throw StaticAnalysisFailedException::with(
				sprintf(
					'Class name must match pattern "%s"',
					$this->regexPattern
				),
				$typeReflection->getName()
			);
		}
	}

}
