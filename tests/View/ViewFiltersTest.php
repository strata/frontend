<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Strata\Frontend\View\ViewFilters;

class ViewFiltersTest extends TestCase
{

    public function testSlugify()
    {
        $helper = new ViewFilters();

        $this->assertEquals('my-name-is-earl', $helper->slugify('My name is Earl'));
        $this->assertEquals('changing-spaces-here-and-here-and-123456-789', $helper->slugify('Changing spaces here "and here" and 123=456-789'));
        $this->assertEquals('a-longer-string-here-and-here', $helper->slugify('A longer string here ' . PHP_EOL . ' and here'));
        $this->assertEquals('my-title', $helper->slugify('<h1>My title</h1>'));
        $this->assertEquals('urljavascriptalertthe-secret-is-to-askwindowlocationreplacedo-something', $helper->slugify('url"javascript:alert(\'the secret is to ask.\');window.location.replace(\'Do something\')'));
        $this->assertEquals('a-lot-of-spaces', $helper->slugify('a lot    of         spaces'));
    }

    public function testFixUrl()
    {
        $helper = new ViewFilters();

        $this->assertEquals('https://www.domain.com', $helper->fixUrl('//www.domain.com'));
        $this->assertEquals('http://www.domain.com', $helper->fixUrl('//www.domain.com', 'http'));
        $this->assertEquals('https://domain.com', $helper->fixUrl('domain.com'));
        $this->assertEquals('http://domain.com', $helper->fixUrl('domain.com', 'http'));
        $this->assertEquals('https://www.domain.com', $helper->fixUrl('www.domain.com'));
        $this->assertEquals('http://www.domain.com', $helper->fixUrl('www.domain.com', 'http'));
        $this->assertEquals('https://domain.com', $helper->fixUrl('domain.com'));
        $this->assertEquals('https://domain.co.uk', $helper->fixUrl('domain.co.uk'));
        $this->assertEquals('https://domain.com/contact/', $helper->fixUrl('domain.com/contact/'));
        $this->assertEquals('https://domain.com/team/bob', $helper->fixUrl('domain.com/team/bob'));
        $this->assertEquals('https://domain.com:8080/about', $helper->fixUrl('domain.com:8080/about'));
        $this->assertEquals('https://domain.com/search?k=maths', $helper->fixUrl('domain.com/search?k=maths'));
        $this->assertEquals('https://domain.com/search?k=maths#3', $helper->fixUrl('domain.com/search?k=maths#3'));
        $this->assertEquals('https://me:pass@staging.domain.com/', $helper->fixUrl('https://me:pass@staging.domain.com/'));
        $this->assertEquals('/team/bob', $helper->fixUrl('/team/bob'));
        $this->assertEquals('../contact', $helper->fixUrl('../contact'));
        $this->assertEquals('fake url', $helper->fixUrl('fake url'));
    }

    public function testExcerpt()
    {
        $helper = new ViewFilters();

        $this->assertEquals('Mary had a little lamb, Its…', $helper->excerpt('Mary had a little lamb, Its fleece was white as snow', 30));
        $this->assertEquals('Mary had a little lamb, [more]', $helper->excerpt('Mary had a little lamb, Its fleece was white as snow', 25, ' [more]'));
    }

    public function testBuildRevision()
    {
        $helper = new ViewFilters();

        $expectedHash = '2f59d2b6';
        $this->assertEquals(__DIR__ . '/assets/styles.css?v=' . $expectedHash, $helper->buildVersion(__DIR__ . '/assets/styles.css'));

        // styles2 = same content
        // styles3 = one change
        $this->assertEquals(__DIR__ . '/assets/styles2.css?v=' . $expectedHash, $helper->buildVersion(__DIR__ . '/assets/styles2.css'));
        $this->assertNotEquals(__DIR__ . '/assets/styles3.css?v=' . $expectedHash, $helper->buildVersion(__DIR__ . '/assets/style3.css'));

        $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/';
        $this->assertEquals('/assets/styles.css?v=' . $expectedHash, $helper->buildVersion('/assets/styles.css'));
    }
}
