<?php

namespace DataSources\Abstracts;

use DataSources\Clients\Interfaces\NewsDataSourceClient;
use Models\NewsArticle;
use Parsers\Interfaces\NewsParser;

/**
 * Class NewsDataSource A news data source. It is assumed that the data source is online and therefore methods for configuring
 * a client, a query to search for, and request method are made available.
 * @package DataSources\Abstracts
 */
abstract class NewsDataSource
{
    /**
     * @var NewsDataSourceClient The news datasource client to be used when making requests.
     */
    private NewsDataSourceClient $newsDataSourceClient;

    /**
     * @var NewsParser The news parser to be used when parsing the consumed resources from the datasource.
     */
    private NewsParser $newsParser;

    /**
     * NewsDataSource constructor.
     * @param NewsDataSourceClient $newsDataSourceClient The news datasource client
     * @param NewsParser $newsParser The news parser
     */
    public function __construct(NewsDataSourceClient $newsDataSourceClient, NewsParser $newsParser)
    {
        $this->newsDataSourceClient = $newsDataSourceClient;
        $this->newsParser = $newsParser;
    }

    /**
     * @return NewsDataSourceClient
     */
    public function getNewsDataSourceClient(): NewsDataSourceClient
    {
        return $this->newsDataSourceClient;
    }

    /**
     * @param NewsDataSourceClient $newsDataSourceClient
     */
    public function setNewsDataSourceClient(NewsDataSourceClient $newsDataSourceClient): void
    {
        $this->newsDataSourceClient = $newsDataSourceClient;
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
     * @return NewsArticle[] The array of retrieved news articles.
     */
    public abstract function retrieveNewsData(): array;
}