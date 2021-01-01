<?php

namespace DataSources\Abstracts;

use GuzzleHttp\Client;
use Parsers\Interfaces\NewsParser;

/**
 * Class NewsDataSource A news data source. It is assumed that the data source is online and therefore methods for configuring
 * a client, a query to search for, and request method are made available.
 * @package DataSources\Abstracts
 */
abstract class NewsDataSource
{
    /**
     * @var string The query to be used when searching the data source.
     */
    private string $query;

    /**
     * @var Client The HTTP client to be used when making external requests.
     * TODO change this client type so it's not stuck to the Guzzle implementation
     */
    private Client $client;

    /**
     * @var string The request method (GET, POST, etc) to be used when making external requests.
     */
    private string $requestMethod;

    /**
     * @var NewsParser The news parser to be used when parsing the consumed resources from the datasource.
     */
    private NewsParser $newsParser;

    /**
     * NewsDataSource constructor.
     * @param string $baseUrl The base URL of the resource to be consumed (DON'T include a site's endpoint when passing this
     * base URL string to the constructor!)
     * @param string $requestMethod The request method.
     * @param NewsParser $newsParser The news parser
     */
    public function __construct(string $baseUrl, string $requestMethod, NewsParser $newsParser)
    {
        $this->client = new Client(["base_uri" => $baseUrl]);
        $this->requestMethod = $requestMethod;
        $this->newsParser = $newsParser;
    }

    /**
     * @return string The query to be used in future searches to the datasource (if any)
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Sets the query to be used.
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return Client The HTTP client to be used when making external requests.
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Sets the HTTP client to be used.
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string The request method (GET, POST, etc) to be used when making external requests.
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @param string $requestMethod The request method.
     */
    public function setRequestMethod(string $requestMethod): void
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return NewsParser The news parser to be used when parsing the consumed resources from the datasource.
     */
    public function getNewsParser(): NewsParser
    {
        return $this->newsParser;
    }

    /**
     * @param NewsParser $newsParser The news parser to be used.
     */
    public function setNewsParser(NewsParser $newsParser): void
    {
        $this->newsParser = $newsParser;
    }

    /**
     * @return array The array of retrieved news articles.
     */
    public abstract function retrieveNewsData(): array;
}