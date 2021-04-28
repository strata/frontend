<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema;

use Strata\Frontend\Exception\ConfigParsingException;
use Strata\Frontend\View\ViewHelpers;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SchemaFactory
{
    /**
     * Return a content model based on YAML config files
     *
     * @param string $file
     * @return Schema
     * @throws ConfigParsingException
     */
    public static function createFromYaml(string $file): Schema
    {
        $configDir = dirname($file);
        try {
            $data = Yaml::parseFile($file);
        } catch (ParseException $e) {
            throw new ConfigParsingException(sprintf('Error parsing content schema YAML config file %s', $file), 0, $e);
        }
        if (!isset($data['content_types'])) {
            throw new ConfigParsingException("Content schema YAML config file must contain a root 'content_types' element");
        }
        if (!isset($data['global'])) {
            throw new ConfigParsingException("Content schema YAML config file must contain a root 'global' element");
        }

        $schema = new Schema();
        foreach ($data['content_types'] as $name => $values) {
            $contentType = new ContentType($name);
            if (isset($values['api_endpoint'])) {
                $contentType->setApiEndpoint($values['api_endpoint']);
            }
            if (isset($values['taxonomies'])) {
                $contentType->setTaxonomies($values['taxonomies']);
            }
            if (isset($values['source_content_type'])) {
                $contentType->setSourceContentType($values['source_content_type']);
            }
            if (isset($values['content_fields'])) {
                $contentType = ContentTypeFactory::addContentFieldsFromYaml($contentType, $configDir . '/' . $values['content_fields']);
            }
            $schema->addItem($contentType);
        }

        if (isset($data['global']) && is_iterable($data['global'])) {
            foreach ($data['global'] as $name => $value) {
                $schema->addGlobal($name, $value);
            }
        }

        return $schema;
    }

    /**
     * Output content schema as YAML config string
     *
     * @param Schema $schema
     * @return string
     */
    public static function toYaml(Schema $schema): string
    {
        $data = [
            'content_types' => [],
            'global' => []
        ];

        foreach ($schema as $contentType) {
            $contentTypeYamlFile = ViewHelpers::slugify($contentType->getName()) . '.yaml';
            $values = [
                'api_endpoint' => $contentType->getApiEndpoint(),
                'content_fields' => $contentTypeYamlFile
            ];
            if (!empty($contentType->getTaxonomies())) {
                $values['taxonomies'] = $contentType->getTaxonomies();
            }
            if (!empty($contentType->getSourceContentType())) {
                $values['source_content_type'] = $contentType->getSourceContentType();
            }
            $data['content_types'] = [$contentType->getName() => $values];
        }
        foreach ($schema->getGlobals() as $key => $value) {
            $data['global'][$key] = $value;
        }

        return Yaml::dump($data, 3);
    }
}
