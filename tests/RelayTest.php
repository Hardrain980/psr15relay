<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'DummyMiddleware.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'DummyRequestHandler.php';

use Leo\Psr15Relay\Relay;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Leo\Psr15Relay\Relay
 */
class RelayTest extends TestCase
{
	public function testCreateRelayWithArrayQueue():void
	{
		$r = new Relay([
			new DummyMiddleware('TestField', '456'),
			new DummyMiddleware('TestField', '123'),
			new DummyRequestHandler('TestField'),
		]);

		$this->assertInstanceOf(Relay::class, $r);
	}

	public function testCreateRelayWithIteratorQueue():void
	{
		$i = new ArrayIterator([
			new DummyMiddleware('TestField', '456'),
			new DummyMiddleware('TestField', '123'),
			new DummyRequestHandler('TestField'),
		]);

		$r = new Relay($i);
		$this->assertInstanceOf(Relay::class, $r);
	}

	public function testThrowExceptionOnInvalidRequestHandler():void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageMatches('/^.*?RequestHandlerInterface.*?$/');

		new Relay([
			new DummyMiddleware('TestField', '456'),
			new DummyMiddleware('TestField', '123'),
			new DummyMiddleware('TestField', '789'),
		]);
	}

	public function testThrowExceptionOnInvalidMiddleware():void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageMatches('/^.*?MiddlewareInterface.*?$/');

		new Relay([
			new DummyMiddleware('TestField', '456'),
			new DummyRequestHandler('Bang'),
			new DummyMiddleware('TestField', '123'),
			new DummyRequestHandler('TestField'),
		]);
	}

	public function testThrowExceptionOnEmptyQueue():void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageMatches('/^.*?EMPTY.*?$/i');

		new Relay([]);
	}

	public function testInitializeWithSingleRequestHandler():void
	{
		$r = new Relay([new DummyRequestHandler('TestField')]);

		$rs = $r->handle(
			new \Nyholm\Psr7\ServerRequest(method:'GET', uri:'https://domain.tld/')
		);

		$this->assertSame('', $rs->getHeaderLine('TestField'));
	}

	public function testInitializeWithRequestHandlerAndMiddlewares():void
	{
		$r = new Relay([
			new DummyMiddleware('TestField', '789'),
			new DummyMiddleware('TestField', '456'),
			new DummyMiddleware('TestField', '123'),
			new DummyRequestHandler('TestField'),
		]);

		$rs = $r->handle(
			new \Nyholm\Psr7\ServerRequest(method:'GET', uri:'https://domain.tld/')
		);

		$this->assertSame('789, 456, 123', $rs->getHeaderLine('TestField'));
	}
}