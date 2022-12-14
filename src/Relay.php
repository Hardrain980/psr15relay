<?php

namespace Leo\Psr15Relay;

use Leo\Psr15Relay\MiddlewareWrapper;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Chaining a PSR-15 request handler and multiple optional PSR-15 middlewares 
 * into a new PSR-15 request handler
 */
class Relay implements RequestHandlerInterface
{
	/**
	 * @var RequestHandlerInterface Top-level middleware wrapper
	 */
	private RequestHandlerInterface $handler;

	/**
	 * @param iterable<MiddlewareInterface|RequestHandlerInterface> $queue
	 */
	public function __construct(iterable $queue)
	{
		if (!is_array($queue))
			$queue = iterator_to_array($queue);

		if (empty($queue))
			throw new \InvalidArgumentException('Queue could not be empty');

		$handler = array_pop($queue);

		if (!($handler instanceof RequestHandlerInterface))
			throw new \InvalidArgumentException('The last item in queue must be an instance of RequestHandlerInterface');

		foreach (array_reverse($queue) as $m) {
			if (!($m instanceof MiddlewareInterface))
				throw new \InvalidArgumentException('The items besides last must be an instance of MiddlewareInterface');

			$handler = new MiddlewareWrapper($m, $handler);
		}

		$this->handler = $handler;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->handler->handle($request);
	}
}
