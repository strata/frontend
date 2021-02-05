<?php
declare(strict_types=1);

namespace Strata\Data\Metadata;

use Strata\Data\Helper\ContentHasher;

class MetadataStatus
{

    /**
     * @var \Strata\Data\Metadata\MetadataRepository
     */
    protected $metadataRepository;

    /**
     * @var \Strata\Data\Helper\ContentHasher
     */
    protected $contentHasher;

    /**
     * MetadataStatus constructor.
     * @param \Strata\Data\Metadata\MetadataRepository $metadataRepository
     * @param \Strata\Data\Helper\ContentHasher $contentHasher
     */
    public function __construct(MetadataRepository $metadataRepository, ContentHasher $contentHasher)
    {
        $this->metadataRepository = $metadataRepository;
        $this->contentHasher = $contentHasher;
    }

    /** @var int */
    const STATUS_NEW = 1;
    const STATUS_CHANGED = 2;
    const STATUS_NOT_CHANGED = 3;
    const STATUS_DELETED = 4;

    /**
     * Determine the status of a piece of metadata
     *
     * @param $id
     * @param $content
     * @return int
     * @throws \Strata\Data\Exception\InvalidMetadataId
     */
    public function getStatus($id, $content): int
    {
        $metaData = $this->metadataRepository->find($id);

        if (empty($metaData)) {
            return self::STATUS_NEW;
        }

        if ($this->contentHasher->hasContentChanged($metaData->getContentHash(), $content)) {
            return self::STATUS_CHANGED;
        }

        return self::STATUS_NOT_CHANGED;

    }
}