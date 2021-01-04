<?php

namespace Parsers;

use DateTime;
use Exception;
use Models\NewsArticle;
use Parsers\Interfaces\NewsParser;

/**
 * Class RapidAPINewsParser Rapid API news parser.
 * @package Parsers
 */
class RapidAPINewsParser implements NewsParser
{
    /**
     * Parses news data from the RapidAPI news datasource.
     * @param $newsData
     * @return NewsArticle[] An array of parsed news articles.
     * @throws Exception
     */
    public function parseNewsData($newsData): array
    {
        $decodedNewsArticles = json_decode($newsData, $flags = JSON_THROW_ON_ERROR);
        $newsArticles = $decodedNewsArticles["news"];
        $newsArray = array();

        for ($i = 0; $i < count($newsArticles); $i++) {
            $currentNewsArticle = $newsArticles[$i];
            $newsArticle = new NewsArticle();
            $newsArticle->setTitle($currentNewsArticle["title"]);
            $newsArticle->setAuthor("");
            $newsArticle->setUrl($currentNewsArticle["webUrl"]);
            $newsArticle->setPublishedAt(new DateTime($currentNewsArticle["publishedDateTime"]));
            $newsArticle->setContent($currentNewsArticle["excerpt"]);
            array_push($newsArray, $newsArticle);
        }

        return $newsArray;
    }
}