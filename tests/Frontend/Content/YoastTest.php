<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Yoast;

class YoastTest extends TestCase
{
    /** @var Wordpress $wordpress */
    private $wordpress;

    private $test_data = [
        [
            "key" => "opengraph-title",
            "value" => "Exploring the Mozambican Miombo"
        ],
        [
            "key" => "opengraph-image",
            "value" => "https://complex.demo/wp-content/uploads/2019/02/the-mozambican-miombo1.jpg"
        ]
    ];

    public function setUp() : void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/posts.1.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/posts.2.json')
            )
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->wordpress = new Wordpress('something');
        $this->wordpress->setClient($client);
    }

    function testYoastClass() {
        $yoast = new Yoast();

        $this->assertInstanceOf(Yoast::class, $yoast);
    }

    function testYoastAddData()
    {
        $yoast = new Yoast();
        $key = $this->test_data[0]["key"];
        $value = $this->test_data[0]["value"];
        $key1 = $this->test_data[1]["key"];
        $value1 = $this->test_data[1]["value"];

        $yoast->add($key, $value);
        $this->assertTrue($yoast->offsetExists($key));
        $this->assertSame($value, $yoast->offsetGet($key));

        $yoast->add($key1, $value1);
        $this->assertTrue($yoast->offsetExists($key1));
        $this->assertSame($value1, $yoast->offsetGet($key1));
    }

    function testAddYoastFromPage() {

        $posts = json_decode(file_get_contents(__DIR__ . '/../responses/flexible-content/posts.1.json'));

        /** @var Yoast[] $yoast_array */
        $yoast_array = [];

        foreach ($posts as $post) {
            if (!isset($post->yoast)) return;
            $yoast_data = (array) $post->yoast;
            $yoast = new Yoast();
            foreach ($yoast_data as $key => $value) {
                if ($value) {
                    $yoast->add($key, $value);
                }
            }

            array_push($yoast_array, $yoast);
        }

        $this->assertSame(10, sizeof($yoast_array));
        $this->assertTrue($yoast_array[7]->offsetExists("opengraph-image"));
        $this->assertTrue($yoast_array[7]->offsetExists("opengraph-title"));
        $this->assertTrue($yoast_array[7]->offsetExists("opengraph-description"));
        $this->assertSame("As 2018 comes to a close, we look back at some of your favourite @FaunaFloraInt Instagram posts from the year...", $yoast_array[7]->offsetGet("opengraph-description"));
        $this->assertSame("https://complex.demo/wp-content/uploads/2019/01/lets-talk-about-the-elephant-that-wasnt-in-the-room-5.png", $yoast_array[3]->offsetGet("twitter-image"));
    }




}