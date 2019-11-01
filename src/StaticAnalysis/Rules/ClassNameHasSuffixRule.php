<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis\Rules;

use Damejidlo\MessageBus\StaticAnalysis\ReflectionHelper;
use Damejidlo\MessageBus\StaticAnalysis\StaticAnalysisFailedException;



class ClassNameHasSuffixRule
{

	/**
	 * @var string
	 */
	private $suffix;



	public function __construct(string $suffix)
	{
		$this->suffix = $suffix;
	}



	/**
	 * @param string $type
	 * @throws StaticAnalysisFailedException
	 */
	public function validate(string $type) : void
	{
		$typeReflection = ReflectionHelper::requireClassReflection($type);

		$matches = [];
		if (! preg_match($this->getRegexPattern(), $typeReflection->getShortName(), $matches)) {
			throw StaticAnalysisFailedException::with(
				sprintf(
					'Class must have suffix "%s"',
					$this->suffix
				),
				$typeReflection->getName()
			);
		}
	}



	public function getRegexPattern() : string
	{
		return sprintf('#^(.*)%s$#', $this->suffix);
	}

}
