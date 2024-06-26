<?php

namespace App\PlatformAPI;

abstract class ActionPaginator
{
    /**
     * @var \App\PlatformAPI\PlatformGateway
     */
    protected $platformGateway;

    protected $firstRound = true;
    protected $totalItemsToQuery;
    protected $itemsQueried;
    protected $currentSkip = 0;
    protected $pageSize = 50;

    protected function getPaginatedItems($apiMethod, $siteId, $params = [])
    {
        $response = $this->platformGateway
            ->withPagination($this->pageSize, $this->currentSkip)
            ->{$apiMethod}($siteId, ...$params);

        $this->updatePageData($response);

        return $response;
    }

    protected function shouldFetch()
    {
        if ($this->firstRound) {
            $this->firstRound = false;

            return true;
        }

        return $this->hasMoreItemsToFetch();
    }

    private function updatePageData($response): void
    {
        $this->totalItemsToQuery = data_get($response, 'pagination.TotalResults', 0);
        $this->itemsQueried = data_get($response, 'pagination.RequestedLimit', 0) + data_get($response, 'pagination.RequestedOffset', 0);

        $this->incrementSkipValue();
    }

    private function hasMoreItemsToFetch(): bool
    {
        return $this->itemsQueried <= $this->totalItemsToQuery;
    }

    private function incrementSkipValue()
    {
        $this->currentSkip += $this->pageSize;
    }
}
