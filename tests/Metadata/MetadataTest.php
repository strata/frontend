<?php

declare(strict_types=1);

namespace Strata\Frontend\Tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Helper\ContentHasher;
use Strata\Frontend\Metadata\Metadata;
use Strata\Frontend\Metadata\MetadataFactory;
use Strata\Frontend\Metadata\MetadataRepository;
use Strata\Frontend\Metadata\MetadataStatus;
use Strata\Frontend\Storage\ArrayStorage;

class MetadataTest extends TestCase
{

    /**
     * @var MetadataFactory
     */
    protected $metaDataFactory;
    /**
     * @var MetadataRepository
     */
    protected $metaDataRepository;

    /**
     * Sets up the properties before the next test runs
     */
    protected function setUp(): void
    {
        $this->metaDataFactory = new MetadataFactory();
        $this->metaDataRepository = new MetadataRepository(new ArrayStorage());
    }

    public function testArrayStorageDataPersistenceWithNoProvidedKey()
    {
        $metaData = $this->metaDataFactory->createNew();
        $metaData->setUrl('https://example.net');
        $metaData->setAttribute('type', 'example_type');

        $this->metaDataRepository->store($metaData);
        $id = $metaData->getId();

        $this->assertTrue($this->metaDataRepository->exists($id));
    }

    public function testArrayStorageDataPersistenceWithProvidedKey()
    {
        $id = rand(0, 999);

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setUrl('https://example.net');
        $metaData->setAttribute('type', 'example_type');
        $metaData->setId($id);

        $this->metaDataRepository->store($metaData);

        $this->assertTrue($this->metaDataRepository->exists($id));
    }

    public function testItemCanBeRetrieved()
    {
        $id = 36;
        $url = 'https://example.net';

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setUrl($url);
        $metaData->setAttribute('type', 'example_type');
        $metaData->setId($id);

        $this->metaDataRepository->store($metaData);

        unset($metaData);

        $metaData = $this->metaDataRepository->find($id);

        $this->assertInstanceOf(Metadata::class, $metaData);
        $this->assertEquals($url, $metaData->getUrl());
    }

    public function testDeleteItemFromStorage()
    {
        $id = 34;
        $this->addExampleItemToStorage($id);

        $this->assertTrue($this->metaDataRepository->exists($id));

        $this->metaDataRepository->delete($id);

        $this->assertFalse($this->metaDataRepository->exists($id));
    }

    public function testCheckIfContentHasChanged()
    {
        $content = 'The quick brown fox jumped over the lazy dog';
        $contentHasher = new ContentHasher();

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setId(23);
        $metaData->setUrl('https://another-example.co.uk');
        $metaData->setAttributes(['attr1' => 'Purple', 'attr2' => 8973]);
        $metaData->setContentHash($contentHasher->hash($content));
        $this->metaDataRepository->store($metaData);

        $identicalContent = $content;
        $differentContent = 'The five boxing wizards jump quickly';

        $this->assertFalse($contentHasher->hasContentChanged($metaData->getContentHash(), $identicalContent));
        $this->assertTrue($contentHasher->hasContentChanged($metaData->getContentHash(), $differentContent));
    }

    public function testContentNew()
    {
        $content = 'The quick brown fox jumped over the lazy dog';
        $id = 1;
        $contentHasher = new ContentHasher();

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setId($id);
        $metaData->setUrl('https://another-example.co.uk');
        $metaData->setAttributes(['attr1' => 'Purple', 'attr2' => 8973]);
        $metaData->setContentHash($contentHasher->hash($content));
        $this->metaDataRepository->store($metaData);

        $newId = 2;
        $newContent = 'Some new content';

        $contentStatus = new MetadataStatus($this->metaDataRepository, $contentHasher);
        $this->assertEquals($contentStatus::STATUS_NEW, $contentStatus->getStatus($newId, $newContent));
    }

    public function testContentChanged()
    {
        $content = 'The quick brown fox jumped over the lazy dog';
        $id = 1;
        $contentHasher = new ContentHasher();

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setId($id);
        $metaData->setUrl('https://another-example.co.uk');
        $metaData->setAttributes(['attr1' => 'Purple', 'attr2' => 8973]);
        $metaData->setContentHash($contentHasher->hash($content));
        $this->metaDataRepository->store($metaData);

        $sameId = 1;
        $newContent = 'Some changed content';

        $contentStatus = new MetadataStatus($this->metaDataRepository, $contentHasher);
        $this->assertEquals($contentStatus::STATUS_CHANGED, $contentStatus->getStatus($sameId, $newContent));
    }

    public function testContentUnchanged()
    {
        $content = 'The quick brown fox jumped over the lazy dog';
        $id = 1;
        $contentHasher = new ContentHasher();

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setId($id);
        $metaData->setUrl('https://another-example.co.uk');
        $metaData->setAttributes(['attr1' => 'Purple', 'attr2' => 8973]);
        $metaData->setContentHash($contentHasher->hash($content));
        $this->metaDataRepository->store($metaData);

        $sameId = 1;
        $sameContent = 'The quick brown fox jumped over the lazy dog';

        $contentStatus = new MetadataStatus($this->metaDataRepository, $contentHasher);
        $this->assertEquals($contentStatus::STATUS_NOT_CHANGED, $contentStatus->getStatus($sameId, $sameContent));
    }

    protected function addExampleItemToStorage($id): Metadata
    {
        $metaData = $this->metaDataFactory->createNew();
        $metaData->setId($id);
        $metaData->setUrl('https://another-example.co.uk');
        $metaData->setAttributes(['attr1' => 'Purple', 'attr2' => 8973]);
        $metaData->setContentHash('asdfjh38f2£F23f23f23f');

        $this->metaDataRepository->store($metaData);

        return $metaData;
    }
}
