<?php

namespace Elder;

class RetryTest extends \PHPUnit_Framework_TestCase
{

	public function tearDown() {
		\Mockery::close();
	}

    public function testNormallyDoesNotRetry()
    {
		$request = \Mockery::mock('\GuzzleHttp\Psr7\Request');
		$response = \Mockery::mock('\GuzzleHttp\Psr7\Response');
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $shouldRetry = Retry::create_decider();
        $this->assertFalse($shouldRetry(
            0,
            $request,
            $response,
            null
        ));
    }

    public function test500Retries()
    {
		$request = \Mockery::mock('\GuzzleHttp\Psr7\Request');
		$response = \Mockery::mock('\GuzzleHttp\Psr7\Response');
        $response->shouldReceive('getStatusCode')->andReturn(500);

        $shouldRetry = Retry::create_decider();
        $this->assertTrue($shouldRetry(
            0,
            $request,
            $response,
            null
        ));
    }

    public function testRetriesLimitedAmountOfTimes()
    {
		$request = \Mockery::mock('\GuzzleHttp\Psr7\Request');
		$response = \Mockery::mock('\GuzzleHttp\Psr7\Response');
        $response->shouldReceive('getStatusCode')->andReturn(500);

        $shouldRetry = Retry::create_decider();
        $this->assertFalse($shouldRetry(
            19,
            $request,
            $response,
            null
        ));
    }

    public function testRetriesOnConnectionException()
    {
		$request = \Mockery::mock('\GuzzleHttp\Psr7\Request');
		$response = \Mockery::mock('\GuzzleHttp\Psr7\Response');
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $shouldRetry = Retry::create_decider();
        $this->assertTrue($shouldRetry(
            0,
            $request,
            $response,
            new \GuzzleHttp\Exception\ConnectException('pretended failure', $request)
        ));
    }
}

