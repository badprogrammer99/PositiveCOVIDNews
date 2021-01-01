<?php

namespace DataSources\Abstracts;

/**
 * Class PaginatedNewsDataSource A paginated news datasource.
 * @package DataSources\Abstracts
 */
abstract class PaginatedNewsDataSource extends NewsDataSource
{
    /**
     * @var int The current page of the datasource.
     */
    private int $currentPage;

    /**
     * The delay (in seconds) at which the Bot should scrape the news. If it's a paginated datasource,
     * then we should have a delay set on in order to avoid being throttled by the API provider.
     */
    private int $delay;

    /**
     * @var int The page limit to which until news should be retrieved.
     */
    private int $pageLimit;

    /**
     * Gets the delay to be used between requests.
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * Sets the delay to be used between requests.
     * @param int $delay
     */
    public function setDelay(int $delay): void
    {
        $this->delay = $delay;
    }

    /**
     * Gets the page limit to which until news should be retrieved.
     * @return int
     */
    public function getPageLimit(): int
    {
        return $this->pageLimit;
    }

    /**
     * Sets the page limit to to which until news should be retrieved.
     * @param int $pageLimit
     */
    public function setPageLimit(int $pageLimit): void
    {
        $this->pageLimit = $pageLimit;
    }

    /**
     * Navigate to the next page.
     */
    public function navigateToNextPage() {
        if ($this->delay > 0)
            sleep($this->delay);
        $this->currentPage++;
    }

    /**
     * Navigate to the previous page if the current page is bigger than 1.
     */
    public function navigateToPreviousPage() {
        if ($this->currentPage > 1) {
            if ($this->delay > 0)
                sleep($this->delay);
            $this->currentPage--;
        }
    }
}