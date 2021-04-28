<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Schema\ContentTypeFactory;
use Strata\Frontend\Schema\Schema;
use Strata\Frontend\Schema\SchemaFactory;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SchemaFactoryTest extends TestCase
{

    public function testConfig()
    {
        $contentModel = SchemaFactory::createFromYaml(__DIR__ . '/config/content_model.yaml');

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
                                                        $this->assertEquals(false, $componentContentFields->getOption('array'));
                                                        break;
                                                }
                                                $a++;
                                            }

                                            break;

                                        case 3:
                                            $this->assertEquals('careers', $component->getName());

                                            $a = 1;
                                            foreach ($component as $componentContentFields) {
                                                switch ($a) {
                                                    case 1:
                                                        $this->assertEquals('careers_list', $componentContentFields->getName());
                                                        $this->assertEquals('relation_array', $componentContentFields->getType());
                                                        $this->assertEquals('career', $componentContentFields->getOption('content_type'));
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
                            case 3:
                                $this->assertEquals('price', $contentField->getName());
                                $this->assertEquals('decimal', $contentField->getType());
                                break;
                            case 4:
                                $this->assertEquals('length', $contentField->getName());
                                $this->assertEquals('decimal', $contentField->getType());
                                $this->assertEquals(4, $contentField->getOption('precision'));
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
        $contentModel = SchemaFactory::createFromYaml(__DIR__ . '/config/content_model_flexible_components.yaml');

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

    public function testGlobals()
    {
        $contentModel = SchemaFactory::createFromYaml(__DIR__ . '/config/content_model.yaml');

        $this->assertSame('myValue', $contentModel->getGlobal('test'));
        $this->assertIsArray($contentModel->getGlobal('image_sizes'));
        $this->assertSame('thumbnail', $contentModel->getGlobal('image_sizes')[0]);
        $this->assertSame('issue-post-image', $contentModel->getGlobal('image_sizes')[6]);
    }

    public function testToYaml()
    {
        $contentModel = SchemaFactory::createFromYaml(__DIR__ . '/config/content_model.yaml');
        $yaml = SchemaFactory::toYaml($contentModel);
        $parsedYaml = Yaml::parse($yaml);

        $this->assertSame(2, count($parsedYaml));
        $this->assertSame('projects', $parsedYaml['content_types']['projects']['api_endpoint']);
        $this->assertSame('projects.yaml', $parsedYaml['content_types']['projects']['content_fields']);
        $this->assertSame('myValue', $parsedYaml['global']['test']);

        $news = $contentModel->getContentType('news');
        $yaml = ContentTypeFactory::toYaml($news);
        $parsedYaml = Yaml::parse($yaml);

        $this->assertSame('boolean', $parsedYaml['exclude_from_search']['type']);
        $this->assertSame('boolean', $parsedYaml['exclude_from_search']['type']);
    }
}
