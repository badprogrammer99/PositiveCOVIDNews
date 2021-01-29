<?php

namespace DataSources;

use DataSources\Abstracts\PaginatedNewsDataSource;
use DataSources\Clients\HttpNewsDataSourceClient;
use Parsers\BBCWorldNewsParser;

class CovidBBCWorldNewsDataSource extends PaginatedNewsDataSource
{
    /**
     * @var string The base URL of the worldwide BBC COVID news.
     */
    private string $baseUrl;

    /**
     * CovidBBCWorldNewsDataSource constructor.
     */
    public function __construct()
    {
        $this->baseUrl = "https://www.bbc.com/news/live/explainers-51871385/page/";
        $this->setRequestOption("REQUEST_METHOD", "GET");
        $this->setDelay(3);
        $this->setCurrentPage(1);
        $this->setPageLimit(5);
        parent::__construct(new HttpNewsDataSourceClient($this->baseUrl), new BBCWorldNewsParser());
    }

    /**
     * {@inheritDoc}
     */
    public function retrievePaginatedNewsData(): array
    {
        $response = $this->getNewsDataSourceClient()->doRequest($this->getCurrentPage(), $this->getRequestOptions());
        return $this->getNewsParser()->parseNewsData($response);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserFriendlyDataSourceName(): string
    {
        return "BBC World News";
    }
}