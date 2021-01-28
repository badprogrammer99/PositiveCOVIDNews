<?php

namespace Parsers;

use DateTime;
use Exception;
use Models\NewsArticle;
use Parsers\Interfaces\NewsParser;

class NewsAPINewsParser implements NewsParser
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function parseNewsData($newsData): array
    {
        $decodedNewsArticles = json_decode($newsData, $flags = JSON_THROW_ON_ERROR);
        $newsArticles = $decodedNewsArticles["articles"];
        $newsArray = [];

        for ($i = 0; $i < count($newsArticles); $i++) {
            $unparsedNewsArticle = $newsArticles[$i];
            $newsArticle = NewsArticle::fromProperties(null,
                $unparsedNewsArticle["title"],
                $unparsedNewsArticle["author"] ?? "",
                $unparsedNewsArticle["url"],
                $unparsedNewsArticle["source"]["name"],
                new DateTime($unparsedNewsArticle["publishedAt"]),
                $unparsedNewsArticle["description"] . "\n\nRead more in: " . $unparsedNewsArticle["url"],
                false);
            $newsArray[] = $newsArticle;
        }

        return $newsArray;
    }
}