<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Schema\ContentType;
use Strata\Frontend\Schema\Field\SchemaField;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;

class CollectionTest extends TestCase
{
    public function testArrayAccess()
    {
        $collection = new ContentType('test');
        $collection->addItem(new SchemaField('test1', 'plaintext'));
        $collection->addItem(new SchemaField('test2', 'plaintext'));
        $collection->addItem(new SchemaField('test3', 'richtext'));

        $this->assertEquals(3, count($collection));
        $this->assertFalse($collection->offsetExists('fake'));

        $x = 1;
        foreach ($collection as $key => $value) {
            $this->assertTrue($value instanceof SchemaFieldInterface);

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
