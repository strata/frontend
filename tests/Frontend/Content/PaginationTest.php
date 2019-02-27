<?php

namespace App\Tests\Frontend\Content;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\Content\Pagination\Pagination;

class PaginationTest extends TestCase
{

    public function testBasicPagination()
    {
        $pages = new Pagination();

        $this->assertEquals(1, $pages->getPage());
        $this->assertEquals(20, $pages->getResultsPerPage());

        $pages->setTotalResults(1039)
              ->setResultsPerPage(10);

        $this->assertEquals(104, count($pages));
        $this->assertEquals(104, $pages->getTotalPages());
        $this->assertEquals(1039, $pages->getTotalResults());
        $this->assertEquals(10, $pages->getResultsPerPage());
        $this->assertEquals(1, $pages->getFrom());
        $this->assertEquals(10, $pages->getTo());
        $this->assertTrue($pages->isFirstPage());

        $pages->setPage(2);
        $this->assertEquals(2, $pages->getPage());
        $this->assertEquals(11, $pages->getFrom());
        $this->assertEquals(20, $pages->getTo());
        $this->assertFalse($pages->isFirstPage());

        $pages->setPage(3);
        $this->assertEquals(3, $pages->getPage());
        $this->assertEquals(21, $pages->getFrom());
        $this->assertEquals(30, $pages->getTo());

        $pages->setResultsPerPage(20);
        $pages->setPage($pages->getTotalPages());
        $this->assertEquals(52, count($pages));
        $this->assertEquals(1021, $pages->getFrom());
        $this->assertEquals(1039, $pages->getTo());
        $this->assertTrue($pages->isLastPage());
    }

    public function testInvalidPageAccess()
    {
        $pages = new Pagination();

        $pages->setTotalResults(1039);
        $this->expectException('Studio24\Frontend\Exception\PaginationException');
        $pages->setPage(100);
    }

    public function testPageLinks()
    {
        $pages = new Pagination();
        $pages->setTotalResults(1039);

        $this->assertEquals([1,2,3,4,5], $pages->getPageLinks());

        $pages->setPage(2);
        $this->assertEquals([1,2,3,4,5], $pages->getPageLinks());

        $pages->setPage(3);
        $this->assertEquals([1,2,3,4,5], $pages->getPageLinks());

        $pages->setPage(6);
        $this->assertEquals([4,5,6,7,8], $pages->getPageLinks());

        $pages->setPage(7);
        $this->assertEquals([5,6,7,8,9], $pages->getPageLinks());

        $pages->setPage(48);
        $this->assertEquals([46,47,48,49,50], $pages->getPageLinks());

        $pages->setPage(52);
        $this->assertEquals([48,49,50,51,52], $pages->getPageLinks());

        $pages->setPage(1);
        $this->assertEquals([1,2,3,4,5,6,7], $pages->getPageLinks(7));
        $pages->setPage(4);
        $this->assertEquals([1,2,3,4,5,6,7], $pages->getPageLinks(7));
        $pages->setPage(5);
        $this->assertEquals([2,3,4,5,6,7,8], $pages->getPageLinks(7));
        $pages->setPage(8);
        $this->assertEquals([5,6,7,8,9,10,11], $pages->getPageLinks(7));
    }

    public function testEmptyPagination()
    {
        $pages = new Pagination();
        $pages->setTotalResults(0);

        $pages->setPage(1);
        $this->assertEquals(1, $pages->getPage());
        $this->assertEquals(0, $pages->getTotalResults());
        $this->assertEquals(1, $pages->getTotalPages());
        $this->assertEquals([], $pages->getPageLinks());
        $this->assertEquals(1, $pages->getNext());
        $this->assertEquals(1, $pages->getLast());
        $this->assertEquals(1, $pages->getFirst());
        $this->assertEquals(1, $pages->getLast());
    }

}
