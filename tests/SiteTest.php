<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Exception\InvalidLocaleException;
use Strata\Frontend\Site;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Languages;

class SiteTest extends TestCase
{
    public function testInvalidTextDirection()
    {
        $site = new Site();

        $this->expectException(\InvalidArgumentException::class);
        $site->addLocale('ja', [], 'atb');
    }

    public function testInvalidLocale()
    {
        $site = new Site();

        $this->expectException(InvalidLocaleException::class);
        $site->addLocale('fake locale string');
    }

    public function testInvalidSiteLocale()
    {
        $site = new Site();
        $site->addLocale('fr');
        $site->addLocale('ja');

        $this->expectException(InvalidLocaleException::class);
        $site->getTextDirection('de');
    }

    public function testTextDirection()
    {
        $site = new Site();
        $site->addLocale('fr');
        $site->addLocale('ja');
        $site->addLocale('ar', [], Site::DIRECTION_RTL);
        $site->addRtfLocale('he');

        $site->setLocale('fr');
        $this->assertSame('ltr', $site->getTextDirection());
        $this->assertSame('', $site->getTextDirectionHtml());

        $site->setLocale('ja');
        $this->assertSame('ltr', $site->getTextDirection());
        $this->assertSame('', $site->getTextDirectionHtml());

        $site->setLocale('ar');
        $this->assertSame('rtl', $site->getTextDirection());
        $this->assertSame('dir="rtl"', $site->getTextDirectionHtml());

        $site->setLocale('he');
        $this->assertSame('rtl', $site->getTextDirection());
    }

    public function testGetLocaleData()
    {
        $site = new Site();
        $site->addLocale('fr', ['siteId' => 1]);
        $site->addLocale('ja', ['siteId' => 2]);
        $site->addRtfLocale('ar', ['siteId' => 3]);

        $site->setLocale('fr');
        $this->assertSame(1, $site->getLocaleData('siteId'));
        $this->assertSame(1, $site->siteId);
        $this->assertNull($site->getLocaleData('test'));

        $site->setLocale('ja');
        $this->assertSame(2, $site->siteId);

        $site->setLocale('ar');
        $this->assertSame(3, $site->siteId);
    }

}
