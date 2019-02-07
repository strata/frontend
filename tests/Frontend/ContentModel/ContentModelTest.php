<?php
declare(strict_types=1);

namespace App\Tests\Frontend\ContentModel;

use PHPUnit\Framework\TestCase;
use Studio24\Frontend\ContentModel\ContentModel;

class ContentModelTest extends TestCase
{

    public function testConfig()
    {
        $contentModel = new ContentModel(__DIR__ . '/config/content_model.yaml');

        $this->assertEquals('myValue', $contentModel->getGlobal('test'));
    }

}
