<?php

namespace View;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\View\ViewTests;

class ViewTestsTest extends TestCase
{

    public function testIsProd()
    {
        $helper = new ViewTests();

        $this->assertTrue($helper->isProd('prod'));
        $this->assertFalse($helper->isProd('stage'));
        $this->assertFalse($helper->isProd('production'));
        $this->assertFalse($helper->isProd(true));
        $this->assertTrue($helper->isProd('production', 'production'));
    }
}
