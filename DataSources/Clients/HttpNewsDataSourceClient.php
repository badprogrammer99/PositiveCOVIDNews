<?php

namespace DataSources\Clients;
use DataSources\Clients\Interfaces\NewsDataSourceClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpNewsDataSourceClient implements NewsDataSourceClient
{
    /**
     * @var string The base URL of the resource we're going to be consuming.
     */
    private string $baseUrl;

    /**
     * @var Client The HTTP client to be used when making external requests.
     */
    private Client $client;

    /**
     * HttpNewsDataSourceClient constructor.
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client(["base_uri" => $this->baseUrl]);
    }

    /**
     * {@inheritDoc}
     * @throws GuzzleException
     */
    public function doRequest(string $dataSourceUri, array $options = [])
    {
        return $this->client->request(
            $options["REQUEST_METHOD"],
            $this->baseUrl . $dataSourceUri,
            $options["ADDITIONAL_OPTIONS"])
        ->getBody();
    }
}