<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\MessageBus\Handling\HandlerType;
use Damejidlo\MessageBus\Handling\MessageType;
use Damejidlo\MessageBus\IMessage;
use Damejidlo\MessageBus\Middleware\MiddlewareContext;



class MessageContextResolver
{

	/**
	 * @var string
	 */
	private $keyPrefix;

	/**
	 * @var PrivateClassPropertiesExtractor
	 */
	private $privateClassPropertiesExtractor;



	public function __construct(
		?PrivateClassPropertiesExtractor $privateClassPropertiesExtractor = NULL,
		string $keyPrefix = ''
	) {
		$this->privateClassPropertiesExtractor = $privateClassPropertiesExtractor ?? new PrivateClassPropertiesExtractor();
		$this->keyPrefix = $keyPrefix;
	}



	/**
	 * @param IMessage $message
	 * @param MiddlewareContext $context
	 * @return mixed[]
	 */
	public function getContext(IMessage $message, MiddlewareContext $context) : array
	{
		$messageType = MessageType::fromMessage($message);

		$result = [
			'messageType' => $messageType->toString(),
		];

		if ($context->has(HandlerType::class)) {
			/** @var HandlerType $handlerType */
			$handlerType = $context->getByType(HandlerType::class);
			$result['handlerType'] = $handlerType->toString();
		}

		$castProperties = $this->privateClassPropertiesExtractor->extract($message);

		$result = $this->mergeSafely($result, $castProperties);

		if ($this->keyPrefix !== '') {
			$result = $this->prefixArrayKeys($result, $this->keyPrefix);
		}

		return $result;
	}



	/**
	 * @param mixed[] $arrayToMergeInto
	 * @param mixed[] $arrayToMerge
	 * @return mixed[]
	 */
	private function mergeSafely(array $arrayToMergeInto, array $arrayToMerge) : array
	{
		$itemsFromArrayToMergeAmbiguousByKey = array_intersect_key($arrayToMerge, $arrayToMergeInto);

		if ($itemsFromArrayToMergeAmbiguousByKey !== []) {
			trigger_error(
				sprintf(
					'Message context merge failed with following duplicate keys: "%s"',
					implode(', ', array_keys($itemsFromArrayToMergeAmbiguousByKey))
				),
				E_USER_WARNING
			);

			$itemsFromArrayToMergeWithDisambiguatedKeys = $this->prefixArrayKeys($itemsFromArrayToMergeAmbiguousByKey, 'disambiguated_');
			$result = $this->mergeSafely($arrayToMergeInto, $itemsFromArrayToMergeWithDisambiguatedKeys);

			$itemsFromArrayToMergeNotAmbiguousByKey = array_diff_key($arrayToMerge, $itemsFromArrayToMergeAmbiguousByKey);
			$result = array_merge($result, $itemsFromArrayToMergeNotAmbiguousByKey);

			return $result;

		}

		return array_merge($arrayToMergeInto, $arrayToMerge);
	}



	/**
	 * @param mixed[] $array
	 * @param string $prefix
	 * @return mixed[]
	 */
	private function prefixArrayKeys(array $array, string $prefix) : array
	{
		$keys = array_map(
			function (string $key) use ($prefix) : string {
				return $prefix . $key;
			},
			array_keys($array)
		);

		$result = array_combine($keys, $array);

		if ($result === FALSE) {
			throw new \LogicException('array_combine failed.');
		}

		return $result;
	}

}
