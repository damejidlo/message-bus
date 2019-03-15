<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\EventBus\SubscriberSpecificDomainEvent;
use Damejidlo\MessageBus\IBusMessage;



class MessageContextResolver
{

	/**
	 * @var MessageTypeResolver
	 */
	private $messageTypeResolver;

	/**
	 * @var string
	 */
	private $keyPrefix;

	/**
	 * @var PrivateClassPropertiesExtractor
	 */
	private $privateClassPropertiesExtractor;



	public function __construct(
		?MessageTypeResolver $messageTypeResolver = NULL,
		?PrivateClassPropertiesExtractor $privateClassPropertiesExtractor = NULL,
		string $keyPrefix = ''
	) {
		$this->messageTypeResolver = $messageTypeResolver ?? new MessageTypeResolver();
		$this->privateClassPropertiesExtractor = $privateClassPropertiesExtractor ?? new PrivateClassPropertiesExtractor();
		$this->keyPrefix = $keyPrefix;
	}



	/**
	 * @param IBusMessage $message
	 * @return mixed[]
	 */
	public function getContext(IBusMessage $message) : array
	{
		$simplifiedMessageType = $this->messageTypeResolver->getSimplifiedMessageType($message);
		$messageType = $this->messageTypeResolver->getMessageType($message);

		$result = [
			sprintf('%sType', $simplifiedMessageType) => $messageType,
		];

		if ($message instanceof SubscriberSpecificDomainEvent) {
			$result['subscriberType'] = $message->getSubscriberType();
			$castProperties = $this->privateClassPropertiesExtractor->extract($message->getEvent());

		} else {
			$castProperties = $this->privateClassPropertiesExtractor->extract($message);
		}

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
