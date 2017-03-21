<?php

namespace Elder;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $file;

    /**
     * @var \Monolog\Logger
     */
    protected $log;

    /**
     * @var \Monolog\Handler\TestHandler
     */
    protected $logHandler;

    public function setUp()
    {
        parent::setUp();
        $this->logHandler = new TestHandler();
        $this->log = new Logger('elder_test');
        $this->log->pushHandler($this->logHandler);
        $this->file = tempnam(sys_get_temp_dir(), 'page');
    }

    public function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testNoConnect()
    {
        $mock = new MockHandler([
            new ConnectException("Error Communicating with Server", new Request('GET', 'test'))
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $page = new Page($this->file);
        $page->load([
            'kb' => 'kb0001',
        ]);
        $proc = new Processor($client, [], $this->log);
        $proc->process($page);

        $this->assertTrue($this->logHandler->hasErrorRecords());
        list($first, $second) = $this->logHandler->getRecords();
        $this->assertContains('Processing', $first['message']);
        $this->assertContains('Failed to connect to', $second['message']);
    }

    public function testGenericException()
    {
        $mock = new MockHandler([
            new \Exception("Something bad happened")
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $page = new Page($this->file);
        $page->load([
            'kb' => 'kb0001',
        ]);
        $proc = new Processor($client, [], $this->log);
        $proc->process($page);

        $this->assertTrue($this->logHandler->hasErrorRecords());
        list($first, $second) = $this->logHandler->getRecords();
        $this->assertContains('Processing', $first['message']);
        $this->assertContains('Failed to generate', $second['message']);
        $this->assertContains('Something bad happened', $second['message']);
    }

    public function test400response()
    {
        $client = $this->getMockClient(400, 'Error 400');

        $page = new Page($this->file);
        $page->load([
            'kb' => 'kb0001',
        ]);

        $proc = new Processor($client, [], $this->log);
        $proc->process($page);

        $this->assertTrue($this->logHandler->hasErrorRecords());
        list($first, $second) = $this->logHandler->getRecords();
        $this->assertContains('Processing', $first['message']);
        $this->assertContains('Failed to generate', $second['message']);
    }

    public function testProperResponse()
    {
        $client = $this->getMockClient(200, "# title\nSome content");

        $page = new Page($this->file);
        $page->load([
            'kb' => 'kb0001',
            'meta' => [
                'custom' => ['front' => 'matter']
            ]
        ]);

        $proc = new Processor($client, [], $this->log);
        $proc->process($page);

        $expected = <<<HEREDOC
---
custom:
    front: matter
notoc: true

---
# title
Some content

HEREDOC;
        $this->assertEquals($expected, file_get_contents($this->file));
    }

    /**
     * @param int $responseCode
     * @param string $body
     * @return \GuzzleHttp\Client
     */
    protected function getMockClient($responseCode, $body)
    {
        $mock = new MockHandler([
            new Response($responseCode, [], $body),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        return $client;
    }
}
