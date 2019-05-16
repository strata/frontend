<?php
declare(strict_types=1);

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Studio24\Frontend\Twig\FrontendExtension;

class TwigTest extends TestCase
{

    public function testSlugify()
    {
        $twig = new FrontendExtension();

        $this->assertEquals('my-name-is-earl', $twig->slugify('My name is Earl'));
        $this->assertEquals('changing-spaces-here-and-here-and-123456-789', $twig->slugify('Changing spaces here "and here" and 123=456-789'));
        $this->assertEquals('a-longer-string-here-and-here', $twig->slugify('A longer string here ' . PHP_EOL . ' and here'));
        $this->assertEquals('my-title', $twig->slugify('<h1>My title</h1>'));
        $this->assertEquals('urljavascriptalertthe-secret-is-to-askwindowlocationreplacedo-something', $twig->slugify('url"javascript:alert(\'the secret is to ask.\');window.location.replace(\'Do something\')'));
        $this->assertEquals('a-lot-of-spaces', $twig->slugify('a lot    of         spaces'));
    }

    public function testFixUrl()
    {
        $twig = new FrontendExtension();

        $this->assertEquals('http://www.domain.com', $twig->fixUrl('//www.domain.com'));
        $this->assertEquals('https://www.domain.com', $twig->fixUrl('//www.domain.com', 'https'));
        $this->assertEquals('http://domain.com', $twig->fixUrl('domain.com'));
        $this->assertEquals('https://domain.com', $twig->fixUrl('domain.com', 'https'));
        $this->assertEquals('http://www.domain.com', $twig->fixUrl('www.domain.com'));
        $this->assertEquals('https://www.domain.com', $twig->fixUrl('www.domain.com', 'https'));
        $this->assertEquals('http://domain.com', $twig->fixUrl('domain.com'));
        $this->assertEquals('http://domain.co.uk', $twig->fixUrl('domain.co.uk'));
        $this->assertEquals('http://domain.com/contact/', $twig->fixUrl('domain.com/contact/'));
        $this->assertEquals('http://domain.com/team/bob', $twig->fixUrl('domain.com/team/bob'));
        $this->assertEquals('http://domain.com:8080/about', $twig->fixUrl('domain.com:8080/about'));
        $this->assertEquals('http://domain.com/search?k=maths', $twig->fixUrl('domain.com/search?k=maths'));
        $this->assertEquals('http://domain.com/search?k=maths#3', $twig->fixUrl('domain.com/search?k=maths#3'));
        $this->assertEquals('https://me:pass@staging.domain.com/', $twig->fixUrl('https://me:pass@staging.domain.com/'));
        $this->assertEquals('/team/bob', $twig->fixUrl('/team/bob'));
        $this->assertEquals('../contact', $twig->fixUrl('../contact'));
        $this->assertEquals('fake url', $twig->fixUrl('fake url'));
    }
}
