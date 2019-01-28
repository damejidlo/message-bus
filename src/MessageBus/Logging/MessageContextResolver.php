<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

use Damejidlo\MessageBus\IBusMessage;
use Damejidlo\MessageBus\Implementation\MessageHashCalculator;



class MessageContextResolver
{

	/**
	 * @var MessageHashCalculator
	 */
	private $messageHashCalculator;

	/**
	 * @var MessageTypeResolver
	 */
	private $messageTypeResolver;

	/**
	 * @var string
	 */
	private $keyPrefix;



	public function __construct(
		?MessageHashCalculator $messageHashCalculator = NULL,
		?MessageTypeResolver $messageTypeResolver = NULL,
		string $keyPrefix = ''
	) {
		$this->messageHashCalculator = $messageHashCalculator ?? new MessageHashCalculator();
		$this->messageTypeResolver = $messageTypeResolver ?? new MessageTypeResolver();
		$this->keyPrefix = $keyPrefix;
	}



	/**
	 * @param IBusMessage $message
	 * @return mixed[]
	 */
	public function getContext(IBusMessage $message) : array
	{
		$messageType = $this->messageTypeResolver->getMessageType($message);

		$result = [
			sprintf('%sType', $messageType) => get_class($message),
			sprintf('%sHash', $messageType) => $this->messageHashCalculator->calculateHash($message),
		];

		if ($message instanceof ILoggableBusMessage) {
			$messageContext = $message->getLoggingContext();
			$result = $this->mergeSafely($result, $messageContext);
		}

		if ($this->keyPrefix !== '') {
			$result = $this->prefixArrayKeys($result, $this->keyPrefix);
		}

		return $result;
	}



	/**
	 * @param mixed[] $array1
	 * @param mixed[] $array2
	 * @return mixed[]
	 */
	private function mergeSafely(array $array1, array $array2) : array
	{
		$keyIntersection = array_intersect_key($array1, $array2);

		if ($keyIntersection !== []) {
			trigger_error(
				sprintf(
					'Message context merge failed with following duplicate keys: "%s"',
					implode(', ', array_keys($keyIntersection))
				),
				E_USER_WARNING
			);

			$array2 = $this->prefixArrayKeys($array2, 'disambiguated_');

			return $this->mergeSafely($array1, $array2);
		}

		return array_merge($array1, $array2);
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
