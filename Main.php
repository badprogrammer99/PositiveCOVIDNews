<?php

require "vendor/autoload.php";

use DataSources\CovidRapidAPINewsDataSource;
use Language\GoogleSentimentAnalyzer;
use Main\Bot;
use Models\NewsArticle;
use Storage\JsonDatabaseNewsStorageSystem;

define("ROOT_DIR", __DIR__);

class Main
{
    public static function main() {
        $newsDataSource = new CovidRapidAPINewsDataSource();
        $newsStorageSystem = new JsonDatabaseNewsStorageSystem();
        $sentimentAnalyzer = new GoogleSentimentAnalyzer();

//        $bot = Bot::createBotFromDataSource($newsDataSource, $newsStorageSystem, $sentimentAnalyzer);
//        $bot->setDelay(30);
//        $bot->run();

        $records = $newsStorageSystem->getAll();
        $newsArticles = array();

        foreach ($records as $record) {
            array_push($newsArticles, new NewsArticle($record));
        }

        return 0;
    }
}

Main::main();