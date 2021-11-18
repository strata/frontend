<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Exception\InvalidLocaleException;
use Strata\Frontend\Site;

class SiteTest extends TestCase
{
    public function testInvalidTextDirection()
    {
        $site = new Site();

        $this->expectException(\InvalidArgumentException::class);
        $site->addLocale('ja', [], 'atb');
    }

    public function testInvalidSiteLocale()
    {
        $site = new Site();
        $site->addLocale('fr');
        $site->addLocale('ja');

        $this->expectException(InvalidLocaleException::class);
        $site->setLocale('de');
    }

    public function testTextDirection()
    {
        $site = new Site();
        $site->addLocale('fr');
        $site->addLocale('ja');
        $site->addLocale('ar', [], Site::DIRECTION_RTL);
        $site->addLocaleRtl('he');

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
        $site->addLocale('fr', ['siteId' => 1, 'test' => 'one']);
        $site->addLocale('ja', ['siteId' => 2, 'test' => 'two']);
        $site->addLocaleRtl('ar', ['siteId' => 3]);

        $site->setLocale('fr');
        $this->assertSame(1, $site->getLocaleData('siteId'));
        $this->assertSame(1, $site->siteId);
        $this->assertTrue($site->hasLocaleData('test'));
        $this->assertSame('one', $site->getLocaleData('test'));

        $site->setLocale('ja');
        $this->assertSame(2, $site->siteId);
        $this->assertTrue($site->hasLocaleData('test'));
        $this->assertSame('two', $site->getLocaleData('test'));

        $site->setLocale('ar');
        $this->assertSame(3, $site->siteId);
        $this->assertFalse($site->hasLocaleData('test'));
        $this->assertNull($site->getLocaleData('test'));
    }

    public function testGetData()
    {
        $site = new Site();
        $site->addLocale('en', [
            'siteId' => 1,
            'baseUrl' => [
                'label' => 'English homepage',
                'url' => 'https://www.example.com/',
            ]
        ]);
        $site->addLocale('ja', [
            'siteId' => 2,
            'baseUrl' => [
                'label' => '日本語ホームページ',
                'url' => 'https://ja.example.com/',
            ]
        ]);
        $site->addLocale('zh-hans', [
            'siteId' => 3,
            'baseUrl' => [
                'label' => '简体中文首页',
                'url' => 'https://zh.example.com/',
            ]
        ]);
        $site->addLocale('de', [
            'siteId' => 4
        ]);
        $site->setLocale('en');

        $expected = [
            'en' => [
                'label' => 'English homepage',
                'url' => 'https://www.example.com/',
            ],
            'ja' => [
                'label' => '日本語ホームページ',
                'url' => 'https://ja.example.com/',
            ],
            'zh-hans' => [
                'label' => '简体中文首页',
                'url' => 'https://zh.example.com/',
            ],
        ];
        $this->assertFalse(array_key_exists('de', $site->getData('baseUrl')));
        $this->assertSame($expected, $site->getData('baseUrl'));

        $expected = [
            'ja' => [
                'label' => '日本語ホームページ',
                'url' => 'https://ja.example.com/',
            ],
            'zh-hans' => [
                'label' => '简体中文首页',
                'url' => 'https://zh.example.com/',
            ],
        ];
        $this->assertSame($expected, $site->getData('baseUrl', true));

        $expected = [
            'en' => [
                'label' => 'English homepage',
                'url' => 'https://www.example.com/',
            ],
            'ja' => [
                'label' => '日本語ホームページ',
                'url' => 'https://ja.example.com/',
            ],
        ];
        $site->setLocale('zh-hans');
        $this->assertSame($expected, $site->getData('baseUrl', true));
    }

    public function testDefaultLocale()
    {
        $site = new Site();
        $site->addLocale('en');
        $this->assertFalse($site->hasDefaultLocale());

        $site->addDefaultLocale('fr');
        $this->assertTrue($site->hasDefaultLocale());
        $this->assertSame('fr', $site->getDefaultLocale());

        $this->assertSame('fr', $site->getLocale());

        // Still fr since once getLocale run sets to default locale
        $site->addLocale('ja');
        $site->addDefaultLocale('de');
        $this->assertTrue($site->hasDefaultLocale());
        $this->assertSame('fr', $site->getLocale());

        // But getDefaultLocale returns whatever is currently set
        $this->assertSame('de', $site->getDefaultLocale());
    }

    public function testGetTextDirection()
    {
        $site = new Site();
        $site->addLocale('en');
        $site->addLocale('ja');
        $site->addLocaleRtl('ar');
        $site->setLocale('en');

        $this->assertSame('ltr', $site->getTextDirection());
        $this->assertSame('rtl', $site->getTextDirection('ar'));
        $this->assertSame('ltr', $site->getTextDirection('ja'));
        $this->assertSame('ltr', $site->getTextDirection('en'));

        $this->expectException(InvalidLocaleException::class);
        $site->getTextDirection('de');
    }

}
