<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\StaticAnalysis;

use Damejidlo\MessageBus\Handling\MessageType;
use Damejidlo\MessageBus\StaticAnalysis\Rules\ClassNameHasSuffixRule;



class MessageNameExtractor
{

	/**
	 * @param MessageType $messageType
	 * @param string $suffix
	 * @return string
	 * @throws StaticAnalysisFailedException
	 */
	public function extract(MessageType $messageType, string $suffix) : string
	{
		$messageTypeReflection = ReflectionHelper::requireClassReflection($messageType->toString());

		$rule = new ClassNameHasSuffixRule($suffix);
		$rule->validate($messageType->toString());

		$matches = [];
		preg_match($rule->getRegexPattern(), $messageTypeReflection->getShortName(), $matches);

		return (string) $matches[1];
	}

}
