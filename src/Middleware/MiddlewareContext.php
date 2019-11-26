<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

final class MiddlewareContext
{

	/**
	 * @var mixed[]
	 */
	private $context = [];



	/**
	 * @param mixed[] $context
	 */
	private function __construct(array $context)
	{
		$this->context = $context;
	}



	public static function empty() : self
	{
		return new self([]);
	}



	/**
	 * Use only for scalar values or when more instances of the same type need to be stored.
	 * @see withValueStoredByType() for more type-safe method of storing values
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return MiddlewareContext
	 */
	public function with(string $key, $value) : self
	{
		$context = new self($this->context);
		$context->context[$key] = $value;

		return $context;
	}



	/**
	 * Only one instance of given type can be stored.
	 *
	 * @param object $value
	 * @return MiddlewareContext
	 */
	public function withValueStoredByType(object $value) : self
	{
		return $this->with(get_class($value), $value);
	}



	public function has(string $key) : bool
	{
		return array_key_exists($key, $this->context);
	}



	/**
	 * Use only for scalar values or when more instances of the same type need to be stored.
	 * @see getByType() for more type-safe method of retrieving values
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get(string $key)
	{
		if (! array_key_exists($key, $this->context)) {
			throw new \OutOfRangeException(sprintf('Key "%s" not found in middleware context.', $key));
		}

		return $this->context[$key];
	}



	public function getByType(string $type) : object
	{
		$value = $this->get($type);

		if (! $value instanceof $type) {
			throw new \LogicException(
				sprintf(
					'Context value has unexpected type "%s", "%s" expected.',
					gettype($value),
					$type
				)
			);
		}

		return $value;
	}

}
