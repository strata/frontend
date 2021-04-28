<?php

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Strata\Data\Http\Http;
use Strata\Frontend\Version;

class UserAgentTest extends TestCase
{
    public function testDataUserAgent()
    {
        $api = new Http('https://example.com/api/');
        $version = $api->getUserAgent();

        $this->assertStringContainsString('Strata_Data', $version);
        $this->assertStringContainsString('(+https://github.com/strata/data)', $version);

        // Only run for Composer 2
        if (class_exists('\Composer\InstalledVersions')) {
            $expects = 'Strata_Data/' . InstalledVersions::getPrettyVersion('strata/data');
            $this->assertStringContainsString($expects, $version);
        }
    }

    public function testFrontUserAgent()
    {
        $userAgent = Version::getUserAgent();

        $this->assertStringContainsString('Strata_Frontend', $userAgent);
        $this->assertStringContainsString('(+https://github.com/strata/frontend)', $userAgent);

        // Only run for Composer 2
        if (class_exists('\Composer\InstalledVersions')) {
            $expects = 'Strata_Frontend/' . InstalledVersions::getPrettyVersion('strata/frontend');
            $this->assertStringContainsString($expects, $userAgent);
        }
    }
}
