<?php

namespace DataSources;

use DataSources\Abstracts\NewsDataSource;
use DataSources\Clients\HttpNewsDataSourceClient;
use Parsers\RapidAPINewsParser;

/**
 * Class CovidRapidAPINewsDataSource Rapid API implementation of the news datasource abstract class.
 * @package DataSources
 */
class CovidRapidAPINewsDataSource extends NewsDataSource
{
    /**
     * @var string The base URL of the COVID Rapid API datasource.
     */
    private string $baseUrl;

    /**
     * @var string The endpoint of the COVID Rapid API datasource to be used in conjunction with the base URL.
     */
    private string $endpoint;

    /**
     * @var string The API key to be used when authenticating the requests to the datasource.
     */
    private string $apiKey;

    /**
     * CovidRapidAPINewsDataSource constructor.
     */
    public function __construct()
    {
        $this->baseUrl = "coronavirus-smartable.p.rapidapi.com";
        $this->endpoint = "/news/v1/global/";
        $this->apiKey = "92b3365ee6mshacd8468fe457effp1104c5jsn5330d8bd5390";
        $this->setRequestOption("REQUEST_METHOD", "GET");
        $this->setRequestOption("ADDITIONAL_OPTIONS", [
            "headers" => [
                "x-rapidapi-key" => $this->apiKey,
                "x-rapidapi-host" => $this->baseUrl
            ]
        ]);
        parent::__construct(new HttpNewsDataSourceClient("https://" . $this->baseUrl), new RapidAPINewsParser());
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveNewsData(): array
    {
        $response = $this->getNewsDataSourceClient()->doRequest($this->endpoint, $this->getRequestOptions());
        return $this->getNewsParser()->parseNewsData($response);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserFriendlyDataSourceName(): string
    {
        return "Rapid API";
    }
}