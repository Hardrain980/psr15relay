<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'DummyMiddleware.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'DummyRequestHandler.php';

use Leo\Psr15Relay\MiddlewareWrapper;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Leo\Psr15Relay\MiddlewareWrapper
 */
class MiddlewareWrapperTest extends TestCase
{
	public MiddlewareWrapper $mw;

	public function setUp():void
	{
		$this->mw = new MiddlewareWrapper(
			new DummyMiddleware('TestField', '123'),
			new DummyRequestHandler('TestField'),
		);
	}

	public function testMiddlewareChained():void
	{
		$r = $this->mw->handle(new \Nyholm\Psr7\ServerRequest(
			method:'GET',
			uri:'https://domain.tld/',
		));

		$this->assertSame('123', $r->getHeaderLine('TestField'));
	}
}
