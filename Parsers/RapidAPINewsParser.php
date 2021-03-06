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
        $newsArray = [];

        for ($i = 0; $i < count($newsArticles); $i++) {
            $unparsedNewsArticle = $newsArticles[$i];
            $newsArticle = NewsArticle::fromProperties(null,
                $unparsedNewsArticle["title"],
                "",
                $unparsedNewsArticle["webUrl"],
                "",
                new DateTime($unparsedNewsArticle["publishedDateTime"]),
                $unparsedNewsArticle["excerpt"],
                false);
            $newsArray[] = $newsArticle;
        }

        return $newsArray;
    }
}