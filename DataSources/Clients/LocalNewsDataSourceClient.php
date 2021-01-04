<?php

namespace DataSources\Clients;

use DataSources\Clients\Interfaces\NewsDataSourceClient;

/**
 * Class LocalNewsDataSourceClient A news datasource client that connects to a local datasource of news (for example,
 * when retrieving a set of mocked news)
 * @package DataSources\Clients
 */
class LocalNewsDataSourceClient implements NewsDataSourceClient
{
    /**
     * {@inheritDoc}
     */
    public function doRequest(string $dataSourceUri, array $options = [])
    {
        $useIncludePath = $options["USE_INCLUDE_PATH"] ?? false;
        $context = $options["CONTEXT"] ?? null;
        $offset = $options["OFFSET"] ?? 0;
        $length = $options["LENGTH"] ?? null;
        return file_get_contents($dataSourceUri, $useIncludePath, $context, $offset, $length);
    }
}