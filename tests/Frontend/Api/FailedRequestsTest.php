<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Api;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Api\Providers\Wordpress;
use Studio24\Frontend\Api\Providers\RestApi;

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
            ),
            new Response(
                404,
                [],
                '{"code":"rest_forbidden","message":"Sorry, you are not allowed to do that.","data":{"status":401}}'
            ),
            new Response(
                500,
                [],
                'Exception error data not found'
            ),
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

    public function testFailedResponsesExceoptionCodes()
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
        $this->expectExceptionCode(404);
        $results = $api->getOne('endpoint', 1);

        $this->expectExceptionCode(500);
        $results = $api->getOne('endpoint', 1);

        $this->expectExceptionCode(401);
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
        $this->expectException(\Studio24\Frontend\Exception\NotFoundException::class);
        $results = $api->getOne('endpoint', 1);

        $this->expectException(\Studio24\Frontend\Exception\FailedRequestException::class);
        $results = $api->getOne('endpoint', 1);

        $this->expectException(\Studio24\Frontend\Exception\FailedRequestException::class);
        $results = $api->getOne('endpoint', 1);
    }
}
