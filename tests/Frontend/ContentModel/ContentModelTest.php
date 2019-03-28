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

        $x = 1;
        foreach ($contentModel as $contentType) {
            switch ($x) {
                case 1:
                    $this->assertEquals('news', $contentType->getName());
                    $this->assertEquals('posts', $contentType->getApiEndpoint());

                    $y = 1;
                    foreach ($contentType as $contentField) {
                        switch ($y) {
                            case 1:
                                $this->assertEquals('theme', $contentField->getName());
                                $this->assertEquals('plaintext', $contentField->getType());
                                break;
                            case 2:
                                $this->assertEquals('description', $contentField->getName());
                                $this->assertEquals('richtext', $contentField->getType());
                                break;
                            case 3:
                                $this->assertEquals('exclude_from_search', $contentField->getName());
                                $this->assertEquals('boolean', $contentField->getType());
                                break;
                            case 4:
                                $this->assertEquals('image', $contentField->getName());
                                $this->assertEquals('image', $contentField->getType());
                                $this->assertTrue(in_array('thumbnail', $contentField->getOption('thumbnails')));
                                break;
                            case 5:
                                $this->assertEquals('page_content', $contentField->getName());
                                $this->assertEquals('flexible', $contentField->getType());

                                $z = 1;
                                foreach ($contentField as $component) {
                                    switch ($z) {
                                        case 1:
                                            $this->assertEquals('content_block', $component->getName());

                                            $a = 1;
                                            foreach ($component as $componentContentFields) {
                                                switch ($a) {
                                                    case 1:
                                                        $this->assertEquals('content_title', $componentContentFields->getName());
                                                        $this->assertEquals('plaintext', $componentContentFields->getType());
                                                        break;
                                                    case 2:
                                                        $this->assertEquals('full_width', $componentContentFields->getName());
                                                        $this->assertEquals('boolean', $componentContentFields->getType());
                                                }
                                                $a++;
                                            }

                                            break;
                                        case 2:
                                            $this->assertEquals('quote_block', $component->getName());

                                            $a = 1;
                                            foreach ($component as $componentContentFields) {
                                                switch ($a) {
                                                    case 1:
                                                        $this->assertEquals('author', $componentContentFields->getName());
                                                        $this->assertEquals('relation', $componentContentFields->getType());
                                                        $this->assertEquals('user', $componentContentFields->getOption('content_type'));
                                                        break;
                                                }
                                                $a++;
                                            }

                                            break;
                                    }
                                    $z++;
                                }

                                break;
                        }

                        $y++;
                    }

                    break;
                case 2:
                    $this->assertEquals('projects', $contentType->getName());
                    $this->assertEquals('projects', $contentType->getApiEndpoint());

                    $y = 1;
                    foreach ($contentType as $contentField) {
                        switch ($y) {
                            case 1:
                                $this->assertEquals('project_area', $contentField->getName());
                                $this->assertEquals('plaintext', $contentField->getType());
                                break;
                            case 2:
                                $this->assertEquals('image', $contentField->getName());
                                $this->assertEquals('image', $contentField->getType());
                                $this->assertTrue(in_array('medium', $contentField->getOption('thumbnails')));
                                $this->assertFalse(in_array('twentyseventeen-featured-image', $contentField->getOption('thumbnails')));
                                break;
                        }

                        $y++;
                    }
                    break;
            }

            $x++;
        }
    }

    public function testYamlConfigKey()
    {
        $contentModel = new ContentModel(__DIR__ . '/config/content_model_flexible_components.yaml');

        $d = 1;
        foreach ($contentModel as $contentType) {
            switch ($d) {
                case 1:
                    //should be the news contentType
                    $c = 1;
                    foreach ($contentType as $contentField) {
                        switch ($c) {
                            case 1:
                                $this->assertEquals('post_type', $contentField->getName());
                                $this->assertEquals('plaintext', $contentField->getType());
                                break;
                            case 16:
                                $this->assertEquals('page_content', $contentField->getName());
                                $this->assertEquals('flexible', $contentField->getType());

                                $b = 1;
                                foreach ($contentField as $component) {
                                    switch ($b) {
                                        case 1:
                                            $this->assertEquals('fullbleed', $component->getName());

                                            $a = 1;
                                            foreach ($component as $componentContentFields) {
                                                switch ($a) {
                                                    case 1:
                                                        $this->assertEquals('title', $componentContentFields->getName());
                                                        $this->assertEquals('text', $componentContentFields->getType());
                                                        break;
                                                    case 2:
                                                        $this->assertEquals('bg_image', $componentContentFields->getName());
                                                        $this->assertEquals('image', $componentContentFields->getType());
                                                }
                                                $a++;
                                            }
                                            break;
                                        case 4:
                                            $this->assertEquals('donation_block', $component->getName());

                                            $a = 1;
                                            foreach ($component as $componentContentFields) {
                                                switch ($a) {
                                                    case 1:
                                                        $this->assertEquals('title', $componentContentFields->getName());
                                                        $this->assertEquals('text', $componentContentFields->getType());
                                                        break;
                                                    case 2:
                                                        $this->assertEquals('text', $componentContentFields->getName());
                                                        $this->assertEquals('text', $componentContentFields->getType());
                                                }
                                                $a++;
                                            }
                                            break;
                                        default:
                                            break;
                                    }
                                    $b++;
                                }
                                break;
                            default:
                                break;
                        }
                        $c++;
                    }
                    break;
                default:
                    break;
            }
            $d++;
        }
    }
}
