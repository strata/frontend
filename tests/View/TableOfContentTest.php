<?php

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Exception\ViewHelperException;
use Strata\Frontend\View\TableOfContents;

class TableOfContentTest extends TestCase
{
    public function testGetHeadings()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));

        $headings = $helper->getParsedHeadings();
        $this->assertSame(7, count($headings));
        $this->assertSame('Rigging', $headings[0]['name']);
        $this->assertSame(2, $headings[0]['level']);
        $this->assertSame('#rigging', $headings[0]['link']);

        $this->assertSame('Look-out position', $headings[4]['name']);
        $this->assertSame(3, $headings[4]['level']);
        $this->assertSame('#look-out-position', $headings[4]['link']);
    }

    public function testDuplicateHeadings()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example-duplicates.html'));

        $headings = $helper->getParsedHeadings();
        $this->assertSame('Ship', $headings[0]['name']);
        $this->assertSame('#ship', $headings[0]['link']);

        $this->assertSame('Ship', $headings[2]['name']);
        $this->assertSame('#ship-1', $headings[2]['link']);

        $this->assertSame('Ship', $headings[3]['name']);
        $this->assertSame('#ship-2', $headings[3]['link']);

        $this->assertSame('Ship', $headings[4]['name']);
        $this->assertSame('#ship-3', $headings[4]['link']);
    }

    public function testTooManyDuplicateHeadings()
    {
        $this->expectException(ViewHelperException::class);
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example-duplicates-too-many.html'));
    }

    public function testHierarchicalHeadings()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));

        $headings = $helper->getHeadings();
        $this->assertSame(5, count($headings));

        $heading = $headings->current();
        $this->assertSame('Rigging', $heading->name);
        $this->assertSame(2, $heading->level);
        $this->assertSame('#rigging', $heading->link);
        $this->assertEmpty($heading->children);

        $headings->next();
        $headings->next();
        $heading = $headings->current();
        $this->assertSame('Ship', $heading->name);
        $this->assertSame(2, $heading->level);
        $this->assertSame('#ship', $heading->link);
        $this->assertSame(2, count($heading->children));

        $subHeading = $heading->children->current();
        $this->assertSame('Topmast', $subHeading->name);
        $this->assertSame(3, $subHeading->level);
        $this->assertSame('#topmast', $subHeading->link);

        $heading->children->next();
        $subHeading = $heading->children->current();
        $this->assertSame('Look-out position', $subHeading->name);
        $this->assertSame(3, $subHeading->level);
        $this->assertSame('#look-out-position', $subHeading->link);

        $headings->next();
        $heading = $headings->current();
        $this->assertSame('Spain', $heading->name);
        $this->assertSame(2, $heading->level);
        $this->assertSame('#spain', $heading->link);
        $this->assertEmpty($heading->children);
    }

    public function testH2()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'), ['h2']);

        $headings = $helper->getParsedHeadings();
        $this->assertSame(5, count($headings));

        $headings = $helper->getHeadings();

        foreach ($headings as $heading) {
            $this->assertEmpty($heading->children);
        }
    }

    public function testH3ToH4()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'), ['h2', 'h3', 'h4']);

        $headings = $helper->getParsedHeadings();
        $this->assertSame(8, count($headings));

        $headings = $helper->getHeadings();
        $headings->next();
        $headings->next();
        $h2 = $headings->current();
        $h3s = $h2->children;
        $h3s->next();
        $h3 = $h3s->current();
        $this->assertSame(1, count($h3->children));
        $h4s = $h3->children;
        $heading = $h4s->current();
        $this->assertSame('Test heading', $heading->name);
        $this->assertSame(4, $heading->level);
        $this->assertSame('#test-heading', $heading->link);
    }

    public function testIncorrectlyNestedHeadings()
    {
        $levels = ['h2', 'h3', 'h4', 'h5'];
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example-incorrect-nesting.html'), $levels);

        $headings = $helper->getParsedHeadings();
        $this->assertSame(7, count($headings));

        $headings = $helper->getHeadings();
        $this->assertSame(3, count($headings));

        $heading = $headings->current();
        $this->assertSame('Rigging', $heading->name);
        $this->assertSame(2, $heading->level);
        $this->assertEmpty($heading->children);

        $headings->next();
        $heading = $headings->current();
        $this->assertSame('Ship', $heading->name);
        $this->assertSame(2, $heading->level);

        $this->assertSame(1, count($heading->children));

        $h3s = $heading->children;
        $subHeading = $h3s->current();
        $this->assertSame('Topmast', $subHeading->name);
        $this->assertSame(3, $subHeading->level);
        $this->assertSame(1, count($subHeading->children));

        $h4s = $subHeading->children;
        $subHeading = $h4s->current();
        $this->assertSame('Jolly Roger', $subHeading->name);
        $this->assertSame(4, $subHeading->level);
    }

    public function testEmptyHeadings()
    {
        $helper = new TableOfContents('<p>Test content</p>');

        $headings = $helper->getParsedHeadings();
        $this->assertEmpty($headings);

        $headings = $helper->getHeadings();
        $this->assertEmpty($headings);
    }

    public function testHtml()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));
        $headings = $helper->getHeadings();

        $html = $helper->html();
        $this->assertStringContainsString('<h2 id="rigging">Rigging</h2>', $html);
        $this->assertStringContainsString('<h2 id="ship">Ship</h2>', $html);
        $this->assertStringContainsString('<h3 id="topmast">Topmast</h3>', $html);
    }

    public function testUl()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));

        $headings = $helper->getHeadings();
        $html = (string) $headings;

        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li><a href="#ship">Ship</a><ul><li><a href="#topmast">Topmast</a></li>', $html);
    }

    public function testUlAttributes()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));

        $headings = $helper->getHeadings();
        $html = $headings->ul(['class' => 'toc', 'role' => 'list']);

        $this->assertStringContainsString('<ul class="toc" role="list">', $html);
        $this->assertStringContainsString('<li><a href="#ship">Ship</a><ul><li><a href="#topmast">Topmast</a></li>', $html);
    }

    public function testDebug()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));
        $html = $helper->html();
        $this->assertStringNotContainsString('<!-- Table of Contents generated for levels h2, h3 -->', $html);

        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'));
        $helper->enableDebug();
        $html = $helper->html();
        $this->assertStringContainsString('<!-- Table of Contents generated for levels h2, h3 -->', $html);

        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example.html'), ['h2']);
        $helper->enableDebug();
        $html = $helper->html();
        $this->assertStringContainsString('<!-- Table of Contents generated for levels h2 -->', $html);
    }

    public function testNestedHeadings()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example-nested-div.html'));
        $html = $helper->html();
        $headings = $helper->getHeadings();
        $this->assertStringContainsString('<a href="#ship">Ship</a', $headings);
        $this->assertStringContainsString('<a href="#topmast">Topmast</a', $headings);
        $this->assertStringContainsString('<h2 id="ship">Ship</h2>', $html);
        $this->assertStringContainsString('<h3 id="topmast">Topmast</h3>', $html);
    }

    public function testExistingIds()
    {
        $helper = new TableOfContents(file_get_contents(__DIR__ . '/html/example-existing-ids.html'));
        $html = $helper->html();
        $headings = $helper->getHeadings();
        $this->assertStringContainsString('<a href="#my-ship">Ship</a', $headings);
        $this->assertStringContainsString('<a href="#custom-link">Topmast</a', $headings);
        $this->assertStringContainsString('<a href="#spain">Spain</a', $headings);
        $this->assertStringContainsString('<h2 id="my-ship">Ship</h2>', $html);
        $this->assertStringContainsString('<h3 id="custom-link">Topmast</h3>', $html);
        $this->assertStringContainsString('<h2 id="spain">Spain</h2>', $html);
    }
}
