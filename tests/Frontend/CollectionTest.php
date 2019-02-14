<?php
declare(strict_types=1);

namespace App\Tests\Frontend;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\ContentModel\ContentType;
use Studio24\Frontend\ContentModel\ContentField;
use Studio24\Frontend\ContentModel\ContentFieldInterface;

class CollectionTest extends TestCase
{
    public function testArrayAccess()
    {
        $collection = new ContentType('test');
        $collection->addItem(new ContentField('test1', 'plaintext'));
        $collection->addItem(new ContentField('test2', 'plaintext'));
        $collection->addItem(new ContentField('test3', 'richtext'));

        $this->assertEquals(3, count($collection));
        $this->assertFalse($collection->offsetExists('fake'));

        $x = 1;
        foreach ($collection as $key => $value) {

            $this->assertTrue($value instanceof ContentFieldInterface);

            switch ($x) {
                case 1:
                    $this->assertEquals('test1', $key);
                    $this->assertEquals('plaintext', $value->getType());
                    break;
                case 2:
                    $this->assertEquals('test2', $key);
                    $this->assertEquals('plaintext', $value->getType());
                    break;
                case 3:
                    $this->assertEquals('test3', $key);
                    $this->assertEquals('richtext', $value->getType());
                    break;
            }
            $x++;
        }
    }

}
