<?php
/**
 * Search contract for models
 */

namespace dezero\contracts;

interface SearchInterface
{
    /**
     * Execte the search and return ActiveDataProvider object or an array of results
     */
    public function search(array $params, ?string $search_id = null);
}
