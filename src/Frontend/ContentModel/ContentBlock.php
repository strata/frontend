<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Exception\UnimplementedException;

/**
 * Represents a content block definition
 *
 * This contains a collection of content fields
 *
 * To keep this simple, we extend ContentType
 *
 * @package Studio24\Frontend\Content
 */
class ContentBlock extends ContentType
{

    public function parseConfig(string $file): ContentType
    {
        throw new UnimplementedException(sprintf('% is not implemented in %s\%s', __METHOD__, __NAMESPACE__, __CLASS__));
    }

    public function getApiEndpoint(): string
    {
        throw new UnimplementedException(sprintf('% is not implemented in %s\%s', __METHOD__, __NAMESPACE__, __CLASS__));
    }

    public function setApiEndpoint(string $apiEndpoint): ContentType
    {
        throw new UnimplementedException(sprintf('% is not implemented in %s\%s', __METHOD__, __NAMESPACE__, __CLASS__));
    }
}
