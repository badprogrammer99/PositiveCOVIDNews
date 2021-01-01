<?php

namespace DataSources;

use DataSources\Abstracts\NewsDataSource;
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
        parent::__construct("", "", new RapidAPINewsParser());
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveNewsData(): array
    {
        $mockedNewsContent = file_get_contents($this->mockDataLocation);
        return $this->getNewsParser()->parseNewsData($mockedNewsContent);
    }
}