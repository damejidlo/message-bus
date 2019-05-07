<?php declare(strict_types = 1);

namespace Damejidlo\MessageBus\Middleware;

class MiddlewareContext
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
