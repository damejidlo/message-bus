<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassNameHasSuffixRule;



class MessageNameExtractor
{

	/**
	 * @param string $messageType
	 * @param string $suffix
	 * @return string
	 * @throws StaticAnalysisFailedException
	 */
	public function extract(string $messageType, string $suffix) : string
	{
		$messageTypeReflection = ReflectionHelper::requireClassReflection($messageType);

		$rule = new ClassNameHasSuffixRule($suffix);
		$rule->validate($messageType);

		$matches = [];
		preg_match($rule->getRegexPattern(), $messageTypeReflection->getShortName(), $matches);

		return (string) $matches[1];
	}

}
