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

        $news = $contentModel->current();
        $field = $news->current();
        $this->assertEquals('post_type', $field->getName());
        $this->assertEquals('plaintext', $field->getType());

        $news->next();
        $field = $news->current();
        $this->assertEquals('theme', $field->getName());
        $this->assertEquals('plaintext', $field->getType());

        $this->assertTrue(isset($news['page_content']));

        $contentModel->next();
        $projects = $contentModel->current();
    }
}
