<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\CraftCms;

use Strata\Data\Collection;
use Strata\Data\Http\Http;
use Strata\Data\Http\Rest;
use Strata\Data\Mapper\MapCollection;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Traits\EventDispatcherTrait;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
use Strata\Frontend\Api\Providers\RestApi;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\RepositoryCommonTrait;
use Strata\Frontend\Data\RepositoryInterface;
use Strata\Frontend\Data\RepositoryMapperInterface;
use Strata\Frontend\Data\WordPress\PageRepositoryMapTrait;
use Strata\Frontend\Schema\Schema;

/**
 * Class to access content from WordPress API
 */
class WordPress implements RepositoryInterface
{
    use RepositoryCommonTrait, EventDispatcherTrait;

    /**
     * Constructor
     *
     * @param string $baseUrl API base URI
     * @param Schema $contentModel Content model
     */
    public function __construct(string $baseUrl, Schema $contentModel = null)
    {
        $this->provider = new Rest($baseUrl);
        $this->respositorySchema = new PageRepositoryMapTrait();

        if ($contentModel instanceof Schema) {
            $this->setContentSchema($contentModel);
        }
    }

    /**
     * Return Strata Data provider to use to retrieve data
     *
     * @return Rest
     */
    public function getProvider(): Rest
    {
        return $this->provider;
    }

    /**
     * Set API authorization to use with all requests
     *
     * Please note WordPress does not usually require authorization for GET requests, so it is recommend to only use
     * this with requests that require authentication.
     *
     * Requires Application Passwords WordPress plugin
     * @see https://wordpress.org/plugins/application-passwords/
     * @param string $token
     */
    public function setAuthorization(string $username, string $password)
    {
        $this->getProvider()->setDefaultOptions([
            'auth_basic' => [$username, $password]
        ]);
    }

    public function getItem(array $data, string $contentPropertyPath): Page
    {
        $mapper = $this->getRepositorySchema()->getItemMapper();
        return $mapper->map($data, $contentPropertyPath);
    }

    /**
     * Return collection of entries
     *
     * @param array $data
     * @param string $contentPropertyPath
     * @param string $totalPropertyPath
     * @return Collection Collection of Page objects
     * @throws \Strata\Data\Exception\MapperException
     */
    public function getList(array $data, string $contentPropertyPath, string $totalPropertyPath): Collection
    {
        $mapper = $this->getRepositorySchema()->getCollectionMapper();
        $mapper->totalResults($totalPropertyPath);
        return $mapper->map($data, $contentPropertyPath);
    }

}
