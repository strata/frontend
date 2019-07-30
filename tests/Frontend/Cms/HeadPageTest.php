<?php
/**
 * Created by PhpStorm.
 * User: bdeboevere
 * Date: 2019-03-11
 * Time: 16:22
 */

namespace App\Tests\Frontend\Cms;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Cms\Wordpress;
use Studio24\Frontend\Content\Page;
use Studio24\Frontend\ContentModel\ContentModel;

class HeadPageTest extends TestCase
{
    /**
     * @var Wordpress $wordpress
     */
    private $wordpress;

    public function setUp() : void
    {
        // Create a mock and queue responses
        $mock = new MockHandler([
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/post.4.json')
            ),
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../responses/flexible-content/post.4.json')
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->wordpress = new Wordpress('something', new ContentModel(__DIR__ . '/config/flexible-content/content_model.yaml'));
        $this->wordpress->setContentType('news');
        $this->wordpress->setClient($client);
    }

    public function testCreatePageWithHead()
    {
//        $json = file_get_contents(__DIR__ . '/../responses/flexible-content/post.3.json');
//        $page_data = (array)json_decode($json);
//        $page = $this->wordpress->createPage($page_data);
        $page = $this->wordpress->getPage(12345);
        $head = $page->getHead();
        $this->assertFalse(empty($head));
        $this->assertSame("On the horizon: looking ahead for global conservation, and hello from yoast", $head->getTitle());
    }

    public function testHeadTwig()
    {
        $page = $this->wordpress->getPage(12345);
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig/templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => __DIR__ . '/twig/cache'
        ]);

        $expected = <<<EOD


EOD;

        $template = $twig->load('head.html.twig');
        $this->assertEquals($expected, $template->render(['page' => new Page()]));

        $expected = <<<EOD
<meta name="description" content="Here is the metadesc, this is short for metadescription">
<meta name="keywords" content="test,mockdata,php,frontend,yoast">
<meta name="twitter:title" content="Did the friendly contest really twist the nation?">
<meta name="twitter:description" content="Is the miss atmosphere better than the summer?">
<meta name="twitter:image" content="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJcAAAB6CAMAAACFmzEXAAAAY1BMVEX///8dofIAnfIAm/EAmPEVn/L5/f/h8f3n9P3x+f6l1fl0v/ZGrvTr9/6XzviJyPdkuPVvvPaSy/i23PpBqvMzp/PT6vy/4PtOsfTH5Pt9wvac0fja7fyKxPZdtfWt2PkAk/BwqbtpAAAEn0lEQVR4nO2byWKrMAxFkW0xE0YzN+n/f+WjIU3TAMEyJn0LzqKr4lwkIcnCWNbBwcHBwcHBwQEZz+3Lsu9d+6+FPOL6aSiBMQRZt7n313JGvEsoOMINZFyc81mrlW9V1Ul2F3XXVlwmysq6e6OsJuQwA/Kz81tVwMBZWGMZO9ALVzsWz7b6sZl//7do8DSymL6+L1odWVEya6xvk8XXm3WrAAZPI7jk9W0J3F//t8llIVuWNcBbq/RTya/xxzWiqxIAgizMq1/LAmBieFBHR7OQLssKvn4AqcKCF06cIHu6rKgY4yEnXeWvWesBFA1dltXcrhaUEIg+CdZi1XiRTUqt9ztHwqMcqJsL4eoJ2/XDiqIrvgeKeroo1YOLya+EGvkJfNJSWPJz6zxUTMrJUj6d0jqNn8LwZLKEJMuqH34DQSkEemVVAIVk1wyGkthohI/3jvykcElHeBgBb3+pqaL+7RMu130p1d14V0fOYM+xgtCtlHGbkiTGNZGeweKJU3j4OsoaQZUFGol1JnMjD14506eUIPhOFVTK2aXgFC1ekVHCflirpvc41nd9nJoMsiVlLUnXrRGjs1BTkEHbzy5J0qXTqI5US+GCjCVzexuaLu39xtCvLoJCtpNqO32Cd9G10ksN/kzz/rGIdJTnkakUkHns1bU5DBtpx7v5dNHxs9du2Dfmq45BHLp1qIPMr0qnpOTVLboUm3Uc1A0gqTqSNw6PeBqVWBFG2zc80cNewjQq9shYJJrdhOnOceB0zQElLWzU0anZXzDOg8tgNHd1A62H7ghRXlNnnZ66cA9Zhaas234IGd/FXCzV1XXax303lPYxszS7pa6rLtIe+xH3RTuxGdR+HC0r3dNgof7YvCFuIygwrQnpiF3sZzB+0del0OZoI5Z3VQqEuxlMbpFlNXsZjGWbdNE6dgK48aWQnexjsWLrez+72EMY3+jGAe+8gyuZxtj+DcIwMfL6NuOG04XYtOX4oa/NKmNmZA3Rn4fMXPxvmABMqVowZTSpNY6bEI0diR11Zhqyrbn+mzhM0jRN6tnxIR2dN7SzVBzJw4cX6Df2z5jtKtDY0ZPcZGJlW8Y4v7HP5gyGtcGTOs7icQgyzOipF2NbXP3h+Dykl/zLoDR83spOzQjTncUtCzMxbDKXuh6EtZuDH8/mZQ3kuM1kKDftGZfp0y0dhc6rWVXKhGtLE9qDJQVsJ6uB6xRxnQNVNGl9HqcJtenhHzvLGimJuoThPD+PR61LfMO0S52emmPfIsuhJliknwWkE2VANBbCltGgGl4sqfWbma/VT9hVysldBQ/3PUnuNXGhseXGbEdZUdl9HYMliwJmuiR6Fz/PqyrPL11QA9Mrish0D7os47bIr+dK2OS0urKxVg5ladIHYku7xWF61N6UslTXWMjl2tG6TZQpNYleVTHZ7dSZ3nFiELRmi4nwLV+YDNkUuKo0xovMwKhZEedyhtVQQ2Qo42ZvBz4RVXEtBJ8Xh8jFJ6T+Wz+4+cFuuvZcDN39D3xIuSDr9JRHf/tBle32ZeWfso82aD8+spNflf/ZN14HBwcHBwcHB/8//wAf2jttaaO4XgAAAABJRU5ErkJggg==">
<meta name="twitter:card" content="summary_large_image">
<meta name="og:title" content="This is a title fot the opengraph-title">
<meta name="og:description" content="A very random description for this field">
<meta name="og:image" content="http://ogp.me/logo.png">


EOD;
        // html needs to be escaped! -> twig escapes html
        $this->assertEquals($expected, $template->render(['page' => $page]));
    }
}
