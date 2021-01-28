<?php

namespace DataSources;

use DataSources\Abstracts\NewsDataSource;
use DataSources\Clients\LocalNewsDataSourceClient;
use Parsers\RapidAPINewsParser;

/**
 * Class MockNewsDataSource Implements a mocked news datasource.
 * @package DataSources
 */
class MockNewsDataSource extends NewsDataSource
{
    /**
     * @var string The location of the mocked news data file.
     */
    private string $mockDataLocation = ROOT_DIR . "/Mocks/MockedNewsData.json";

    /**
     * MockNewsDataSource constructor.
     */
    public function __construct()
    {
        parent::__construct(new LocalNewsDataSourceClient(), new RapidAPINewsParser());
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveNewsData(): array
    {
        $mockedNewsContent = $this->getNewsDataSourceClient()->doRequest($this->mockDataLocation);
        return $this->getNewsParser()->parseNewsData($mockedNewsContent);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserFriendlyDataSourceName(): string
    {
        return "Mocked source, to be used for testing purposes";
    }
}