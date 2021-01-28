<?php

namespace Main;

use DataSources\Abstracts\NewsDataSource;
use Language\Interfaces\SentimentAnalyzer;
use Language\SentimentScore;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class Bot The class responsible for periodically scraping news articles. So far, and considering the thematic of this
 * project, the Bot will scrape positive news about COVID-19, with the help of the Google Cloud Natural Language API.
 */
class Bot
{
    /**
     * @var NewsDataSource[] The array of news data sources to be used when scrapping the websites.
     */
    private array $newsDataSources;

    /**
     * @var NewsStorageSystem[] The array of news storage systems to be used when saving the news.
     */
    private array $newsStorageSystems;

    /**
     * @var SentimentAnalyzer The sentiment analyzer used to analyze the various processed news.
     */
    private SentimentAnalyzer $sentimentAnalyzer;

    /**
     * @var int The delay before moving on to the next news data source.
     */
    private int $delay;

    /**
     * Private bot constructor.
     * @param NewsDataSource[] $newsDataSources
     * @param NewsStorageSystem[] $newsStorageSystems
     * @param SentimentAnalyzer $sentimentAnalyzer
     */
    private function __construct(array $newsDataSources, array $newsStorageSystems, SentimentAnalyzer $sentimentAnalyzer)
    {
        $this->newsDataSources = $newsDataSources;
        $this->newsStorageSystems = $newsStorageSystems;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    /**
     * Instantiates a bot with multiple data sources.
     * @param NewsDataSource[] $newsDataSources
     * @param NewsStorageSystem[] $newsStorageSystems
     * @param SentimentAnalyzer $sentimentAnalyzer
     * @return Bot
     */
    public static function createBot(array $newsDataSources, array $newsStorageSystems,
                                     SentimentAnalyzer $sentimentAnalyzer): Bot
    {
        Utils::checkIfArrayIsComposedOfNewsDataSourceInstances($newsDataSources);
        Utils::checkIfArrayIsComposedOfNewsStorageSystemInstances($newsStorageSystems);

        return new Bot($newsDataSources, $newsStorageSystems, $sentimentAnalyzer);
    }

    /**
     * Add a NewsDataSource to be used by the bot when retrieving news.
     * @param NewsDataSource $newsDataSource
     */
    public function addNewsDataSource(NewsDataSource $newsDataSource)
    {
        $this->newsDataSources[] = $newsDataSource;
    }

    /**
     * Adds an array of NewsDataSource objects to be used by the bot when retrieving news.
     * @param NewsDataSource[] $newsDataSources
     */
    public function addNewsDataSources(array $newsDataSources)
    {
        Utils::checkIfArrayIsComposedOfNewsDataSourceInstances($newsDataSources);

        foreach ($newsDataSources as $newsDataSource)
            $this->newsDataSources[] = $newsDataSource;
    }

    /**
     * Adds a NewsStorageSystem to be used by the bot when saving news.
     * @param NewsStorageSystem $newsStorageSystem
     */
    public function addNewsStorageSystem(NewsStorageSystem $newsStorageSystem)
    {
        $this->newsStorageSystems[] = $newsStorageSystem;
    }

    /**
     * Adds an array of NewsStorageSystem to be used by the bot when saving news.
     * @param array $newsStorageSystems
     */
    public function addNewsStorageSystems(array $newsStorageSystems)
    {
        Utils::checkIfArrayIsComposedOfNewsStorageSystemInstances($newsStorageSystems);

        foreach ($newsStorageSystems as $newsStorageSystem)
            $this->newsStorageSystems[] = $newsStorageSystem;
    }

    /**
     * Get the delay to be applied when searching between data sources.
     * @return int
     */
    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @param int $delay the delay (in seconds) to be applied when searching between data sources.
     */
    public function setDelay(int $delay): void {
        $this->delay = $delay;
    }

    /**
     * Starts the bot execution.
     * Due to the blocking single-threaded nature of the PHP language, we can't create background threads, so this bot will
     * block execution for a while while retrieving news articles from a datasource, especially on paginated data sources which have
     * delayed requests.
     */
    public function run() {
        foreach ($this->newsDataSources as $newsDataSource) {
            $newsArticlesData = $newsDataSource->retrieveNewsData();
            foreach ($newsArticlesData as $newsArticle) {
                $title = $newsArticle->getTitle();
                $content = $newsArticle->getContent();
                $titleSentiment = $this->sentimentAnalyzer->getSentimentForText($title);
                $contentSentiment = $this->sentimentAnalyzer->getSentimentForText($content);
                if ($titleSentiment == SentimentScore::POSITIVE() && $contentSentiment == SentimentScore::POSITIVE()) {
                    foreach ($this->newsStorageSystems as $newsStorageSystem) {
                        $newsStorageSystem->insert($newsArticle);
                    }
                }
            }

            if ($this->delay > 0) {
                sleep($this->delay);
            }
        }
    }
}