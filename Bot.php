<?php

use DataSources\Abstracts\NewsDataSource;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class Bot The class responsible for periodically scraping news articles. So far, and considering the thematic of this
 * project, the Bot will scrape positive news about COVID-19, with the help of the Google Cloud Natural Language API.
 */
class Bot
{
    /**
     * @var array The news datasources to be used when scrapping the websites.
     */
    private array $newsDataSources;

    /**
     * @var NewsStorageSystem The news storage system to be used when saving the news.
     */
    private NewsStorageSystem $newsStorageSystem;

    /**
     * @var int The delay before moving on to the next news data source.
     */
    private int $delay;

    /**
     * Private bot constructor.
     * @param array $newsDataSources
     * @param NewsStorageSystem $newsStorageSystem
     */
    private function __construct(array $newsDataSources, NewsStorageSystem $newsStorageSystem) {
        $this->newsDataSources = $newsDataSources;
        $this->newsStorageSystem = $newsStorageSystem;
    }

    /**
     * Instantiates a bot with a single datasource.
     * @param NewsDataSource $newsDataSource
     * @param NewsStorageSystem $newsStorageSystem
     * @return Bot
     */
    public static function createBotWithOneDataSource(NewsDataSource $newsDataSource, NewsStorageSystem $newsStorageSystem): Bot {
        return new Bot(array($newsDataSource), $newsStorageSystem);
    }

    /**
     * Instantiates a bot with multiple datasources.
     * @param array $newsDataSources
     * @param NewsStorageSystem $newsStorageSystem
     * @return Bot
     */
    public static function createBotWithMultipleDataSources(array $newsDataSources, NewsStorageSystem $newsStorageSystem): Bot {
        $newsDataSourcesToBeUsed = array();

        foreach ($newsDataSources as $newDataSource) {
            Utils::checkIfObjIsNewsDataSourceInstance($newDataSource);
            array_push($newsDataSourcesToBeUsed, $newDataSource);
        }

        return new Bot($newsDataSourcesToBeUsed, $newsStorageSystem);
    }

    /**
     * Get the delay to be applied when searching between datasources.
     * @return int
     */
    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @param int $delay sets the delay to be applied when searching between datasources.
     */
    public function setDelay(int $delay): void {
        $this->delay = $delay;
    }

    /**
     * Add a NewsDataSource to be used by the bot when retrieving news.
     * @param NewsDataSource $newsDataSource
     */
    public function addNewsDataSource(NewsDataSource $newsDataSource) {
        array_push($this->newsDataSources, $newsDataSource);
    }

    /**
     * Adds an array of NewsDataSource objects to be used by the bot when retrieving news.
     * @param array $newsDataSources
     */
    public function addNewsDataSources(array $newsDataSources) {
        foreach ($newsDataSources as $newDataSource) {
            Utils::checkIfObjIsNewsDataSourceInstance($newDataSource);
            array_push($this->newsDataSources, $newDataSource);
        }
    }

    /**
     * Starts the bot execution.
     * Due to the blocking single-threaded nature of the PHP language, we can't create background threads, so this bot will
     * block execution for a while while retrieving news articles from a datasource, especially on paginated datasources which have
     * delayed requests.
     */
    public function run() {
        while (true) {
            foreach ($this->newsDataSources as $newsDataSource) {
                $newsArticlesData = $newsDataSource->retrieveNewsData();

                $this->newsStorageSystem->insertAll($newsArticlesData);

                if ($this->delay > 0) {
                    sleep($this->delay);
                }
            }
        }
    }
}