<?php
/**
 * Search contract for models
 */

namespace dezero\contracts;

interface SearchInterface
{
    /**
     * Execute the search and return ActiveDataProvider object or an array of results
     */
    public function search(array $params, ?string $search_id = null);
}
