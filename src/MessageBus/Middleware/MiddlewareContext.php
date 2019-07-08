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

}
