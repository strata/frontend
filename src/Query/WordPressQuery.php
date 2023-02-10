<?php

declare(strict_types=1);

namespace Strata\Frontend\Query;

use Strata\Data\Query\Query;

class WordPressQuery extends Query
{
    private string $fieldParameter = '_fields';
    private string $resultsPerPageParam = 'per_page';

    /**
     * Pagination in headers:
     * X-WP-Total: the total number of records in the collection
     * X-WP-TotalPages: the total number of pages encompassing all available records
     */
    private string $totalResultsPropertyPath = '[X-WP-Total]';
    private string $totalPagesPropertyPath = '[X-WP-TotalPages]';
}
