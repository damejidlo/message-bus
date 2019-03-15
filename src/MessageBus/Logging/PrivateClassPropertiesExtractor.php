<?php
declare(strict_types = 1);

namespace Damejidlo\MessageBus\Logging;

class PrivateClassPropertiesExtractor
{

	/**
	 * @param object $object
	 * @return mixed[]
	 */
	public function extract(object $object) : array
	{
		// magic :)
		$extract = \Closure::bind(
			function ($object) {
				return get_object_vars($object);
			},
			NULL,
			$object
		);

		return $extract($object);
	}

}
