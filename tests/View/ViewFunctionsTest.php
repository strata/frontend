<?php

namespace View;

class ViewFunctionsTest
{

    public function testStagingBanner()
    {
        $helper = new ViewFunctions();

        $banner = $helper->stagingBanner('staging');
        $this->assertStringContainsString('This is the <strong>staging</strong> environment', $banner);
        $this->assertStringContainsString('class="staging-banner staging"', $banner);

        $banner = $helper->stagingBanner('prod');
        $this->assertEmpty($banner);

        $banner = $helper->stagingBanner('live', 'You are on %s', 'live');
        $this->assertEmpty($banner);

        $banner = $helper->stagingBanner('staging', 'You are on %s');
        $this->assertStringContainsString('You are on staging', $banner);

        $banner = $helper->stagingBanner('Made up Env');
        $this->assertStringContainsString('class="staging-banner made-up-env"', $banner);
    }


    public function testNotEmpty()
    {
        $helper = new ViewFilters();

        $this->assertTrue($helper->notEmpty(null, null, '', 'test'));
        $this->assertFalse($helper->notEmpty(null, null, '', ''));
        $this->assertFalse($helper->notEmpty(null, null, '', '0'));
        $this->assertFalse($helper->notEmpty(null, null, '', 0));
    }

    public function testAllNotEmpty()
    {
        $helper = new ViewFilters();

        $this->assertFalse($helper->allNotEmpty(null, false, '', 'test'));
        $this->assertFalse($helper->allNotEmpty(null, 'hello there', 'test', 20));
        $this->assertTrue($helper->allNotEmpty('hello', 'there', 'general', 'kenobi'));
        $this->assertTrue($helper->allNotEmpty('hello', 'there', 42));
        $this->assertTrue($helper->allNotEmpty('hello', 'there', 42, true));
    }
}
