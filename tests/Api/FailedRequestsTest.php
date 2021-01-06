<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Api;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Strata\Frontend\Api\Providers\Wordpress;
use Strata\Frontend\Api\Providers\RestApi;
use Strata\Frontend\Exception\ApiException;
use Strata\Frontend\Exception\NotFoundException;

class FailedRequestsTest extends TestCase
{

    public function testFailedResponses()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                401,
                [],
                '{"code":"rest_forbidden","message":"Sorry, you are not allowed to do that.","data":{"status":401}}'
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Wordpress('somewhere');
        $api->setClient($client);

        // Test it!
        $api->ignoreErrorCode(401);
        $results = $api->getMedia(1000);
        $this->assertEmpty($results);
    }

    public function testFailedResponsesExceptionCodes401404()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                401,
                [],
                'Exception error data not found'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestApi('somewhere');
        $api->setClient($client);

        // 401 are ignored by default so shouldn't stop execution
        $results = $api->getOne('endpoint', 1);

        $this->expectExceptionCode(404);
        $results = $api->getOne('endpoint', 1);
    }

    public function testFailedResponsesExceptionCodes500()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                500,
                [],
                'Exception error data not found'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestApi('somewhere');
        $api->setClient($client);

        $this->expectExceptionCode(500);
        $results = $api->getOne('endpoint', 1);
    }

    public function testFailedResponsesExceptions()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                500,
                [],
                'Exception error data not found'
            ),
            new Response(
                401,
                [],
                'Exception error data not found'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestApi('somewhere');
        $api->setClient($client);

        // Test it!
        $this->expectException(\Strata\Frontend\Exception\NotFoundException::class);
        $results = $api->getOne('endpoint', 1);

        $this->expectException(\Strata\Frontend\Exception\FailedRequestException::class);
        $results = $api->getOne('endpoint', 1);

        $this->expectException(\Strata\Frontend\Exception\FailedRequestException::class);
        $results = $api->getOne('endpoint', 1);
    }

    public function testResetIgnoreExceptionCodes()
    {
        // Create a mock and queue two responses
        $mock = new MockHandler([
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                '{"code":"rest_invalid_param","message":"page not found","data":{"status":404}}'
            ),
            new Response(
                404,
                [],
                'Exception error data not found'
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new RestApi('somewhere');
        $api->setClient($client);

        try {
            $results = $api->getOne('endpoint', 1);
            $this->fail('Didn\'t catch 404 exception');
        } catch (NotFoundException $e) {
        }

        //simply checking execution hasn't stopped above (checking no exception was thrown)
        $this->assertTrue(true);

        $api->ignoreErrorCode(404);
        $results = $api->getOne('endpoint', 1);

        //simply checking execution hasn't stopped above (checking no exception was thrown)
        $this->assertTrue(true);

        $api->restoreDefaultIgnoredErrorCodes();

        try {
            $results = $api->getOne('endpoint', 1);
            $this->fail();
        } catch (NotFoundException $e) {
        }

        //simply checking execution hasn't stopped above (checking no exception was thrown)
        $this->assertTrue(true);
    }
}
