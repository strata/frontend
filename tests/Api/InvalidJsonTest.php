<?php

declare(strict_types=1);

namespace App\Tests\Frontend\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Strata\Frontend\Api\Providers\RestApi;
use Strata\Frontend\Exception\FailedRequestException;

class InvalidJsonTest extends TestCase
{

    protected $jsonMalformed = <<<EOD
[{"id":14433,"date":"2020-01-29T14:56:05","date_gmt":"2020-01-29T14:56:05","guid":{"rendered":"http:\/\/www.domain.com\/?page_id=123"},"modified":"2020-01-29T17:24:25","modified_gmt":"2020-01-29T17:24:25","slug":"test-page-2","status":"publish","type":"page","link":"https:\/\/www.domain.com\/test-page-2\/","title":{"rendered":"Test Page 2"},"content":{"rendered":"Test content here","protected":false},"excerpt":{"rendered":"","protected":false},"author":123,}]
EOD;

    protected $jsonPhpNotice = <<<EOD
<br />
<b>Notice</b>:  Undefined index: file in <b>/var/www/public/wp-includes/media.php</b> on line <b>1500</b><br />
<br />
<b>Notice</b>:  Undefined index: file in <b>/var/www/public/wp-includes/media.php</b> on line <b>1500</b><br />
[{"id":14433,"date":"2020-01-29T14:56:05","date_gmt":"2020-01-29T14:56:05","guid":{"rendered":"http:\/\/www.domain.com\/?page_id=123"},"modified":"2020-01-29T17:24:25","modified_gmt":"2020-01-29T17:24:25","slug":"test-page-2","status":"publish","type":"page","link":"https:\/\/www.domain.com\/test-page-2\/","title":{"rendered":"Test Page 2"},"content":{"rendered":"Test content here","protected":false},"excerpt":{"rendered":"","protected":false},"author":123}]
EOD;

    public function testInvalidJson()
    {
        // Create a mock and queue a response
        $mock = new MockHandler([
            new Response(
                200,
                [],
                $this->jsonMalformed
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->expectException(FailedRequestException::class);
        $api = new RestApi('/test');
        $response = $client->get('/test');
        $api->parseJsonResponse($response);
    }

    public function testJsonPhpError()
    {
        // Create a mock and queue a response
        $mock = new MockHandler([
            new Response(
                200,
                [],
                $this->jsonPhpNotice
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->expectException(FailedRequestException::class);

        $api = new RestApi('/test');
        $response = $client->get('/test');
        $api->parseJsonResponse($response);
    }
}
