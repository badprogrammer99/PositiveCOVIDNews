<?php

namespace Parsers;

use DateTime;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Models\NewsArticle;
use Parsers\Interfaces\NewsParser;
use stdClass;

/**
 * Class BBCWorldNewsParser BBC World News parser.
 * @package Parsers
 */
class BBCWorldNewsParser implements NewsParser
{
    /**
     * @var DOMDocument The DOM document representing an active instance of the page.
     */
    private DOMDocument $domDocument;

    /**
     * @var DOMXPath The selector to be used when querying the DOM document for elements.
     */
    private DOMXPath $selector;

    /**
     * BBCWorldNewsParser constructor.
     */
    public function __construct()
    {
        $this->domDocument = new DOMDocument();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function parseNewsData($newsData): array
    {
        @$this->domDocument->loadHTML($newsData);
        $this->selector = new DOMXPath($this->domDocument);
        $articles = $this->selector->query("//*[contains(@class, 'lx-stream__post-container')]");

        $newsArray = [];

        foreach ($articles as $article) {
            if ($this->selector->query(".//*[contains(@class, 'smp-embed')]", $article)->length == 0) {
                $articleHeader = $this->getHeaderInfoFromArticle($article);
                $articleContent = $this->getContentInfoFromArticle($article);
                $articleDate = $this->getDateOfArticle($article);
                $newsArticle = NewsArticle::fromProperties(null,
                    $articleHeader->title,
                    "",
                    $articleHeader->link,
                    "BBC News",
                    new DateTime($articleDate),
                    $articleContent->text . "\n\nRead more in: " . $articleHeader->link,
                    false);
                $newsArray[] = $newsArticle;
            }
        }

        return $newsArray;
    }

    /**
     * Extracts the header information of the BBC article.
     * @param $article DOMElement The HTML of the BBC article.
     * @return stdClass An object with the title of the article and the link, if applicable (some articles don't have
     * a link).
     */
    private function getHeaderInfoFromArticle(DOMElement $article)
    {
        $anchorXPathQuery = ".//*[contains(@class, 'lx-stream-post__header-link')]";
        $titleXPathQuery = ".//*[contains(@class, 'lx-stream-post__header-text')]";
        $articleAnchor = $this->selector->query($anchorXPathQuery, $article)[0];
        $parsedArticleHeader = new stdClass();

        if ($articleAnchor == null) {
            $parsedArticleHeader->title = $this->selector->query($titleXPathQuery, $article)[0]->nodeValue;
            $parsedArticleHeader->link = "";
        } else {
            $parsedArticleHeader->title = $articleAnchor->nodeValue;
            $parsedArticleHeader->link = "https://www.bbc.com" . $articleAnchor->getAttribute("href");
        }

        return $parsedArticleHeader;
    }

    /**
     * Extracts the content information of the BBC article.
     * @param $article DOMElement The HTML of the BBC article.
     * @return stdClass An object with the content of the article.
     */
    private function getContentInfoFromArticle(DOMElement $article)
    {
        $storyXPathQuery = ".//*[contains(@class, 'lx-stream-related-story--summary')]";
        $bodyXPathQuery = ".//*[contains(@class, 'lx-stream-post-body')]";
        $articleContent = $this->selector->query($storyXPathQuery, $article)[0];
        $parsedArticleContent = new stdClass();

        $articleContent != null
            ? $parsedArticleContent->text = $articleContent->nodeValue
            : $parsedArticleContent->text = $this->joinParagraphTexts($this->selector->query($bodyXPathQuery, $article)[0]);

        return $parsedArticleContent;
    }

    /**
     * Extracts the date of the BBC article.
     * @param DOMElement $article The HTML of the BBC article.
     * @return mixed
     */
    private function getDateOfArticle(DOMElement $article)
    {
        return $this->selector
            ->query(".//*[contains(@class, 'qa-post-auto-meta')]", $article)[0]
            ->nodeValue;
    }

    /**
     * Small utility function that joins all the texts from the HTML paragraphs present inside of another element.
     * @param DOMElement $domElement The element containing paragraph elements.
     * @return string The combined text.
     */
    private function joinParagraphTexts(DOMElement $domElement): string
    {
        $text = "";

        foreach ($domElement->getElementsByTagName("p") as $paragraph) {
            $text .= $paragraph->nodeValue;
        }

        return $text;
    }
}