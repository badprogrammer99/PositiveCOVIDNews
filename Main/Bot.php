<?php

namespace Main;

use DataSources\Abstracts\NewsDataSource;
use Language\Interfaces\SentimentAnalyzer;
use Language\SentimentScore;
use SplQueue;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class Bot The class responsible for periodically scraping news articles. So far, and considering the thematic of this
 * project, the Bot will scrape positive news about COVID-19, with the help of the Google Cloud Natural Language API.
 */
class Bot
{
    /**
     * @var SplQueue The queue of news datasources to be used when scrapping the websites.
     */
    private SplQueue $newsDataSources;

    /**
     * @var NewsStorageSystem The news storage system to be used when saving the news.
     */
    private NewsStorageSystem $newsStorageSystem;

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
     * @param SplQueue $newsDataSources
     * @param NewsStorageSystem $newsStorageSystem
     * @param SentimentAnalyzer $sentimentAnalyzer
     */
    private function __construct(SplQueue $newsDataSources, NewsStorageSystem $newsStorageSystem, SentimentAnalyzer $sentimentAnalyzer) {
        $this->newsDataSources = $newsDataSources;
        $this->newsStorageSystem = $newsStorageSystem;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    /**
     * Instantiates a bot with a single datasource.
     * @param NewsDataSource $newsDataSource
     * @param NewsStorageSystem $newsStorageSystem
     * @param SentimentAnalyzer $sentimentAnalyzer
     * @return Bot
     */
    public static function createBotFromDataSource(NewsDataSource $newsDataSource, NewsStorageSystem $newsStorageSystem,
                                                      SentimentAnalyzer $sentimentAnalyzer): Bot {
        $newsDataSources = new SplQueue();
        $newsDataSources->push($newsDataSource);
        return new Bot($newsDataSources, $newsStorageSystem, $sentimentAnalyzer);
    }

    /**
     * Instantiates a bot with multiple datasources.
     * @param array $newsDataSources
     * @param NewsStorageSystem $newsStorageSystem
     * @param SentimentAnalyzer $sentimentAnalyzer
     * @return Bot
     */
    public static function createBotFromMultipleDataSources(array $newsDataSources, NewsStorageSystem $newsStorageSystem,
                                                            SentimentAnalyzer $sentimentAnalyzer): Bot {
        $newsDataSourcesToBeUsed = new SplQueue();

        Utils::checkIfArrayIsComposedOfNewsDataSourceInstances($newsDataSources);

        foreach ($newsDataSources as $newsDataSource)
            $newsDataSourcesToBeUsed->push($newsDataSource);

        return new Bot($newsDataSourcesToBeUsed, $newsStorageSystem, $sentimentAnalyzer);
    }

    /**
     * Add a NewsDataSource to be used by the bot when retrieving news.
     * @param NewsDataSource $newsDataSource
     */
    public function addNewsDataSource(NewsDataSource $newsDataSource) {
        $this->newsDataSources->push($newsDataSource);
    }

    /**
     * Adds an array of NewsDataSource objects to be used by the bot when retrieving news.
     * @param array $newsDataSources
     */
    public function addNewsDataSources(array $newsDataSources) {
        Utils::checkIfArrayIsComposedOfNewsDataSourceInstances($newsDataSources);

        foreach ($newsDataSources as $newsDataSource)
            $this->newsDataSources->push($newsDataSource);
    }

    /**
     * Get the delay to be applied when searching between datasources.
     * @return int
     */
    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @param int $delay the delay (in seconds) to be applied when searching between datasources.
     */
    public function setDelay(int $delay): void {
        $this->delay = $delay;
    }

    /**
     * Starts the bot execution.
     * Due to the blocking single-threaded nature of the PHP language, we can't create background threads, so this bot will
     * block execution for a while while retrieving news articles from a datasource, especially on paginated datasources which have
     * delayed requests.
     */
    public function run() {
        while (count($this->newsDataSources) > 0) {
            foreach ($this->newsDataSources as $newsDataSource) {
                $newsArticlesData = $newsDataSource->retrieveNewsData();

                foreach ($newsArticlesData as $newsArticle) {
                    $content = $newsArticle->getContent();
                    $sentiment = $this->sentimentAnalyzer->getSentimentForText($content);
                    // echo $sentiment->getValue() . PHP_EOL;

                    /** @noinspection PhpNonStrictObjectEqualityInspection */
                    if ($sentiment == SentimentScore::POSITIVE()) {
                        // echo "POSITIVE NEWS FOUND! THE CONTENT IS AS IT FOLLOWS: " . PHP_EOL . $content . PHP_EOL;
                        $this->newsStorageSystem->insert($newsArticle);
                    }
                }

                $this->newsDataSources->pop();

                if ($this->delay > 0) {
                    sleep($this->delay);
                }
            }
        }
    }
}