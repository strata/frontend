<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema;

use Strata\Frontend\Content\Field\ArrayContent;
use Strata\Frontend\Schema\ContentFieldCollection;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Content\Field\FlexibleContent;
use Strata\Frontend\Content\Field\RelationArray;
use Strata\Frontend\Exception\ConfigParsingException;
use Strata\Frontend\Schema\Field\ArraySchemaField;
use Strata\Frontend\Schema\Field\SchemaField;
use Strata\Frontend\Schema\Field\SchemaFieldInterface;
use Strata\Frontend\Schema\Field\FlexibleSchemaField;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ContentTypeFactory
{
    /**
     * Parse the content fields YAML config file for this content type
     *
     * @param ContentType $contentType Content type to add content fields to
     * @param string $file Content fields YAML config file
     * @return ContentType
     * @throws ConfigParsingException
     */
    public static function addContentFieldsFromYaml(ContentType $contentType, string $file): ContentType
    {
        $configDir = dirname($file);
        try {
            $data = Yaml::parseFile($file);
        } catch (ParseException $e) {
            throw new ConfigParsingException(sprintf('Error parsing content fields YAML config file %s', $file), 0, $e);
        }
        if (!is_array($data)) {
            throw new ConfigParsingException("Content fields YAML config file must contain an array of content fields");
        }

        foreach ($data as $name => $values) {
            if (!is_array($values)) {
                throw new ConfigParsingException(sprintf("Content field definition must contain an array of values, including the 'type' property, %s found", gettype($values)));
            }
            $contentType->addItem(self::parseContentFieldArray($contentType, $name, $values, $configDir));
        }

        return $contentType;
    }

    /**
     * Parse an array into a Content SchemaField object
     *
     * @param string $name Content field name
     * @param array $data Content field data
     * @param string $configDir Path to config directory
     * @return SchemaFieldInterface
     * @throws ConfigParsingException
     */
    public static function parseContentFieldArray(ContentType $contentType, string $name, array $data, string $configDir = '.'): SchemaFieldInterface
    {
        if (isset($data['config'])) {
            $data = YAML::parseFile($configDir . '/' . $data['config']);
        }
        if (!isset($data['type'])) {
            throw new ConfigParsingException("You must set a 'type' for a content type, e.g. type: plaintext");
        }
        if (!$contentType->validContentFields($data['type'])) {
            throw new ConfigParsingException(sprintf("Invalid content field type '%s'", $data['type']));
        }

        switch ($data['type']) {
            case FlexibleContent::TYPE:
                if (!isset($data['components'])) {
                    throw new ConfigParsingException("You must set a 'components' array for a flexible content field");
                }
                $contentField = new FlexibleSchemaField($name, $data['components']);
                break;

            case ArrayContent::TYPE:
                if (!isset($data['content_fields'])) {
                    throw new ConfigParsingException("You must set a 'content_fields' array for an array content field");
                }
                $contentField = new ArraySchemaField($name, $data['content_fields']);
                break;

            default:
                // Validation
                if ($data['type'] === RelationArray::TYPE && !isset($data['content_type'])) {
                    throw new ConfigParsingException("You must set a 'content_type' array for a relation array content field");
                }

                $contentField = new SchemaField($name, $data['type']);

                unset($data['type']);
                if (is_array($data)) {
                    foreach ($data as $name => $value) {
                        $contentField->addOption($name, $value);
                    }
                }
        }

        return $contentField;
    }

    /**
     * Output content type as YAML config
     *
     * @return string
     */
    public static function toYaml(ContentType $contentType): string
    {
        $data = [];
        foreach ($contentType as $contentField) {
            $data[$contentField->getName()] = ContentTypeFactory::contentFieldToArray($contentField);
        }
        return Yaml::dump($data, 6);
    }

    /**
     * Return content field object as array ready to convert into YAML
     *
     * @param SchemaFieldInterface $contentField
     * @return array
     */
    public static function contentFieldToArray(SchemaFieldInterface $contentField): array
    {
        $values = [
            'type' => $contentField->getType()
        ];
        foreach ($contentField->getOptions() as $name => $value) {
            $values[$name] = $value;
        }

        if ($contentField instanceof FlexibleSchemaField) {
            $values['components'] = [];

            /** @var ContentFieldCollection $collection */
            foreach ($contentField as $collection) {
                $values['components'][$collection->getName()] = [];

                // build array of component data
                /** @var ContentFieldInterface $field */
                foreach ($collection as $field) {
                    $values['components'][$collection->getName()][$field->getName()] = ContentTypeFactory::contentFieldToArray($field);
                }
            }
        }
        if ($contentField instanceof ArraySchemaField) {
            $values['content_fields'] = [];

            /** @var ContentFieldCollection $collection */
            foreach ($contentField as $collection) {
                /** @var SchemaFieldInterface $field */
                foreach ($collection as $field) {
                    $values['content_fields'][$field->getName()] = ContentTypeFactory::contentFieldToArray($field);
                }
            }
        }
        return $values;
    }
}
