<?php

namespace Studio24\Frontend\Content\Translation;

use Studio24\Frontend\Content\Field\ShortText;
use Studio24\Frontend\ContentModel\ContentModel;
use Studio24\Frontend\ContentModel\FieldInterface;

class ContentFieldTranslator
{
    use BasicContentFieldTranslationsTrait;

    /**
     * @var \Studio24\Frontend\ContentModel\ContentModel
     */
    protected $contentModel;

    /**
     * @param \Studio24\Frontend\ContentModel\ContentModel $contentModel
     */
    public function setContentModel(ContentModel $contentModel)
    {
        $this->contentModel = $contentModel;
    }

    /**
     * @return \Studio24\Frontend\ContentModel\ContentModel
     */
    public function getContentModel(): ContentModel
    {
        return $this->contentModel;
    }

    /**
     * Takes a content model object and returns a content field
     */
    public function resolveContentField(FieldInterface $contentModelField, $value) {
        $methodName = 'resolve' . ucfirst($contentModelField->getType()) . 'Field';

        return $this->$methodName($contentModelField, $value);
    }

}