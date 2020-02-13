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

    public function testExcerpt()
    {
        $twig = new FrontendExtension();

        $this->assertEquals('Mary had a little lamb, Its…', $twig->excerpt('Mary had a little lamb, Its fleece was white as snow', 30));
        $this->assertEquals('Mary had a little lamb, [more]', $twig->excerpt('Mary had a little lamb, Its fleece was white as snow', 25, ' [more]'));
    }

    public function testBuildRevisionFilter()
    {
        $twig = new FrontendExtension();

        $this->assertEquals(__DIR__ . '/assets/styles.css?v=2f59d2b6', $twig->buildVersion(__DIR__ . '/assets/styles.css'));

        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/';
        $this->assertEquals('/assets/styles.css?v=2f59d2b6', $twig->buildVersion('/assets/styles.css'));
    }

    public function testIsProd()
    {
        $twig = new FrontendExtension();

        $this->assertTrue($twig->isProd('prod'));
        $this->assertFalse($twig->isProd('stage'));
        $this->assertFalse($twig->isProd('production'));
        $this->assertFalse($twig->isProd(true));
        $this->assertTrue($twig->isProd('production', 'production'));
    }

    public function testStagingBanner()
    {
        $twig = new FrontendExtension();

        $banner = $twig->stagingBanner('staging');
        $this->assertStringContainsString('This is the <strong>staging</strong> environment', $banner);
        $this->assertStringContainsString('class="staging-banner staging"', $banner);

        $banner = $twig->stagingBanner('prod');
        $this->assertEmpty($banner);

        $banner = $twig->stagingBanner('live', 'You are on %s', 'live');
        $this->assertEmpty($banner);

        $banner = $twig->stagingBanner('staging', 'You are on %s');
        $this->assertStringContainsString('You are on staging', $banner);

        $banner = $twig->stagingBanner('Made up Env');
        $this->assertStringContainsString('class="staging-banner made-up-env"', $banner);
    }

    public function testNotEmpty()
    {
        $twig = new FrontendExtension();

        $this->assertTrue($twig->notEmpty(null, null, '', 'test'));
        $this->assertFalse($twig->notEmpty(null, null, '', ''));
        $this->assertFalse($twig->notEmpty(null, null, '', '0'));
        $this->assertFalse($twig->notEmpty(null, null, '', 0));
    }

    public function testAllNotEmpty()
    {
        $twig = new FrontendExtension();

        $this->assertFalse($twig->allNotEmpty(null, false, '', 'test'));
        $this->assertFalse($twig->allNotEmpty(null, 'hello there', 'test', 20));
        $this->assertTrue($twig->allNotEmpty('hello', 'there', 'general', 'kenobi'));
        $this->assertTrue($twig->allNotEmpty('hello', 'there', 42));
        $this->assertTrue($twig->allNotEmpty('hello', 'there', 42, true));
    }
}
