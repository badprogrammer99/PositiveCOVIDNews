<?php

namespace DataSources;

use DataSources\Abstracts\PaginatedNewsDataSource;
use DataSources\Clients\HttpNewsDataSourceClient;
use Parsers\NewsAPINewsParser;

/**
 * Class CovidNewsAPIDataSource
 * @package DataSources
 */
class CovidNewsAPIDataSource extends PaginatedNewsDataSource
{
    /**
     * @var string The base URL of the News API website.
     */
    private string $baseUrl;

    /**
     * @var string The API key to be used when making requests to the News API website.
     */
    private string $apiKey = "4ad8415b67c24c8a88dad69e6bc3f67f";

    /**
     * CovidNewsAPIDataSource constructor.
     */
    public function __construct()
    {
        $this->baseUrl = "https://newsapi.org/v2/everything?q=coronavirus&language=en&sortBy=publishedAt&apiKey=".$this->apiKey."&page=";
        $this->setRequestOption("REQUEST_METHOD", "GET");
        $this->setDelay(4);
        $this->setPageLimit(5);
        parent::__construct(new HttpNewsDataSourceClient($this->baseUrl), new NewsAPINewsParser());
    }

    /**
     * @inheritDoc
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
        return "News API";
    }
}