<?php

namespace Leo\Psr15Relay;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareWrapper implements RequestHandlerInterface
{
	public function __construct(
		private MiddlewareInterface $middleware,
		private RequestHandlerInterface $next_handler,
	)
	{

	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->middleware->process($request, $this->next_handler);
	}
}
