<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Strata\Frontend\View\ViewFilters;

class ViewFiltersTest extends TestCase
{

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
