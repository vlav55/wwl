<?php

namespace Pact\Http;

class QueryBuilder
{
    /**
     * Replaces indexes in query string for arrays
     * 
     * Since http_build_query generating indexes for each element, some servers
     * recognize it as associative array, this method fixes this behavior by dirtyhacking.
     * 
     * @todo Reimplement http_build_query to remove dirtyhack if needed
     */
    public function build(array $queryData)
    {
        $queryString = http_build_query($queryData);

        return $this->removeMarkedIndexes($queryString);
    }

    private function removeMarkedIndexes(string $queryString)
    {
        return preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '%5B%5D=', $queryString);
    }
}
