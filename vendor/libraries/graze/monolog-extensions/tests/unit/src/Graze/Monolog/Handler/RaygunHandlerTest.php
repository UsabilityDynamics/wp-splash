<?php
namespace Graze\Monolog\Handler;

use Mockery as m;
use Monolog\TestCase;

class RaygunHandlerTest extends TestCase
{
    public function setUp()
    {
        if (!class_exists('Raygun4php\RaygunClient')) {
            $this->markTestSkipped('mindscape/raygun4php not installed');
        }

        $this->client = m::mock('Raygun4php\RaygunClient');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Graze\\Monolog\\Handler\\RaygunHandler', new RaygunHandler($this->client));
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Monolog\\Handler\\HandlerInterface', new RaygunHandler($this->client));
    }

    public function testGetFormatter()
    {
        $handler = new RaygunHandler($this->client, 'foo');
        $this->assertInstanceOf('Monolog\\Formatter\\NormalizerFormatter', $handler->getFormatter());
    }

    public function testHandleError()
    {
        $record = $this->getRecord(300, 'foo', array('file' => 'bar', 'line' => 1));
        $formatter = m::mock('Monolog\\Formatter\\FormatterInterface');
        $handler = new RaygunHandler($this->client);
        $handler->setFormatter($formatter);

        $formatter
             ->shouldReceive('format')
             ->once()
             ->with($record)
             ->andReturn(array());
        $this->client
             ->shouldReceive('SendError')
             ->once()
             ->with(0, 'foo', 'bar', 1);

        $handler->handle($record);
    }

    public function testHandleException()
    {
        $exception = new \Exception('foo');
        $record = $this->getRecord(300, 'foo', array('exception' => $exception));
        $formatter = m::mock('Monolog\\Formatter\\FormatterInterface');
        $handler = new RaygunHandler($this->client);
        $handler->setFormatter($formatter);

        $formatter
             ->shouldReceive('format')
             ->once()
             ->with($record)
             ->andReturn(array());
        $this->client
             ->shouldReceive('SendException')
             ->once()
             ->with($exception);

        $handler->handle($record);
    }
}