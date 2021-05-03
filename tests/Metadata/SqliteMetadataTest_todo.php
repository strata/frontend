<?php

declare(strict_types=1);

namespace Strata\Frontend\Tests;

use PHPUnit\Framework\TestCase;
use Strata\Frontend\Metadata\MetadataFactory;
use Strata\Frontend\Metadata\MetadataRepository;
use Strata\Frontend\Storage\SQLiteStorage;

class SqliteMetadataTestTodo extends TestCase
{

    /**
     * @var MetadataFactory
     */
    protected $metaDataFactory;
    /**
     * @var MetadataRepository
     */
    protected $metaDataRepository;

    protected $dbFileLocation = '';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dbFileLocation = __DIR__ . '/database.sqlite';
    }

    /**
     * Sets up the properties before the next test runs
     */
    protected function setUp(): void
    {
        $this->metaDataFactory = new MetadataFactory();

        $sqliteStorage = new SQLiteStorage();
        touch($this->dbFileLocation);
        $sqliteStorage->init(['filename' => $this->dbFileLocation]);
        $this->metaDataRepository = new MetadataRepository($sqliteStorage);
    }

    /**
     * Removes the DB before the next test runs
     */
    protected function tearDown(): void
    {
        unlink($this->dbFileLocation);
    }

    public function testDataPersistenceWithId()
    {
        $id = 482;

        $metaData = $this->metaDataFactory->createNew();
        $metaData->setUrl('https://example.net');
        $metaData->setAttribute('type', 'example_type');
        $metaData->setId($id);

        $this->metaDataRepository->store($metaData);

        $this->assertTrue($this->metaDataRepository->exists($id));
    }

    public function testDataPersistenceWithNoProvidedId()
    {
        $metaData = $this->metaDataFactory->createNew();
        $metaData->setUrl('https://example.net');
        $metaData->setAttribute('type', 'example_type');

        $this->metaDataRepository->store($metaData);
        $id = $metaData->getId();

        $this->assertTrue($this->metaDataRepository->exists($id));
    }
}
