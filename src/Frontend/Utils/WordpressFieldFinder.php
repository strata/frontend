<?php

namespace Studio24\Frontend\Utils;

use Webmozart\Assert\Assert;

/**
 * Class FieldFinder
 * @package Studio24\Frontend\Utils
 */
class WordpressFieldFinder
{

    /**
     * Attempt to find the id field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function id(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['id'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data, 'integer');
    }

    /**
     * Attempt to find a data modified field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function title(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['title' => ['rendered'], 'post_title', 'post_name'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find a data modified field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function dateModified(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['modified', 'post_modified'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find a data published field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function datePublished(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['date', 'post_date'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find the slug field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function slug(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['slug', 'post_name'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find the status field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function status(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['status', 'post_status'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find the type field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function type(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['type', 'post_type'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }

    /**
     * Attempt to find the excerpt field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function excerpt(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['excerpt' => ['rendered'], 'post_excerpt'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data);
    }


    /**
     * Attempt to find the content field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function content(array $data, array $searchFields = null): ?string
    {
        if (empty($searchFields)) {
            $searchFields = ['content' => ['rendered'], 'post_content'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data, 'string');
    }

    /**
     * Attempt to find the author field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function author(array $data, array $searchFields = null): ?int
    {
        if (empty($searchFields)) {
            $searchFields = ['author', 'post_author'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data, 'integer');
    }

    /**
     * Attempt to find the featured image field from an array of data
     *
     * @param array $data
     * @param array $searchFields
     * @return null|string
     */
    public static function featuredImage(array $data, array $searchFields = null): ?int
    {
        if (empty($searchFields)) {
            $searchFields = ['featured_media'];
        }

        return self::findFirstFieldFromSearch($searchFields, $data, 'integer');
    }

    /**
     * Find the first field from a data array which matches a field in a search array
     * Search is case insensitive - this helps with fields such as id, Id, ID
     *
     * @param array $searchFields in order of preference
     * @param array $data
     * @param string $type
     * @return null|string
     */
    protected static function findFirstFieldFromSearch(array $searchFields, array $data, $type = 'string'): ?string
    {
        // Lets lowercase the data array keys for easier searching;
        $data = array_change_key_case($data, CASE_LOWER);

        foreach ($searchFields as $searchFieldKey => $searchField) {
            // Lets decide what we're searching on, the value or key.
            $needle = $searchField;
            if (is_array($needle)) {
                $needle = $searchFieldKey;
            }

            // Lets check if the key isn't there, and continue if it isn't
            if (!array_key_exists(strtolower($needle), $data)) {
                continue;
            }

            // The needle was found
            $potentialValue = $data[strtolower($needle)];

            // Did we find an array or a value;
            if (is_array($potentialValue)) {
                // We're looking more than one layer deep, so lets keep searching
                return self::findFirstFieldFromSearch($searchField, $potentialValue);
            }

            // Was the result what we expected? Or should we continue looking?
            try {
                Assert::notEmpty($potentialValue);

                switch ($type) {
                    case 'string':
                        Assert::string($potentialValue);
                        break;
                    case 'integer':
                        if(is_string($potentialValue) && is_numeric($potentialValue)) {
                            $potentialValue = (int) $potentialValue;
                        }
                        Assert::integer($potentialValue);
                        break;
                    default:
                        Assert::string($potentialValue);
                        break;
                }
            } catch (\InvalidArgumentException $e) {
                // Move on to the next variable
                continue;
            }

            // We found it
            return $potentialValue;
        }

        // Nothing was found
        return null;
    }
}
