<?php

namespace DataSources\Clients\Interfaces;

/**
 * Interface NewsDataSourceClient The client that is responsible for making requests to the news datasource client.
 * @package DataSources\Interfaces
 */
interface NewsDataSourceClient
{
    /**
     * Makes a request to the datasource.
     * @param string $dataSourceUri The URI of the datasource. This can be an external website, a database, or simply
     * a local file.
     * @param array $options The options to be used when connecting to the datasource. For example, if we're connecting
     * to a database then we should pass the credentials of the database, or if we're connecting to a external website instead,
     * then we should pass the request configurations (request methods, authentication, headers), etc.
     * @return mixed
     */
    public function doRequest(string $dataSourceUri, array $options = []);
}