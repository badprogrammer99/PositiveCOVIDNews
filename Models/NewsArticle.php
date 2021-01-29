<?php

namespace Models;

use DateTime;
use Exception;
use JsonSerializable;

/**
 * Class NewsArticle Represents a news article.
 * @package Models
 */
class NewsArticle implements JsonSerializable
{
    /**
     * @var ?int The ID of the news article.
     */
    private ?int $id;

    /**
     * @var string Title of the news article.
     */
    private string $title;

    /**
     * @var string Author of the news article (if any are mentioned).
     */
    private string $author;

    /**
     * @var string Original URL of the news article.
     */
    private string $url;

    /**
     * @var string The source of the news article.
     */
    private string $source;

    /**
     * @var DateTime Time at which the news article was published.
     */
    private DateTime $publishedAt;

    /**
     * @var string Content of the news article.
     */
    private string $content;

    /**
     * @var bool Was this news article already republished in some blog?
     */
    private bool $active;

    /**
     * Constructs an instance of this class using already initialized properties.
     * @param $id ?int
     * @param $title string
     * @param $author string
     * @param $url string
     * @param $source string
     * @param $publishedAt DateTime
     * @param $content string
     * @param $active bool
     * @return NewsArticle The already constructed news article.
     */
    public static function fromProperties(?int $id, string $title, string $author, string $url,
                                          string $source, DateTime $publishedAt, string $content, bool $active): NewsArticle
    {
        $newsArticle = new NewsArticle();
        $newsArticle->setId($id);
        $newsArticle->setTitle($title);
        $newsArticle->setAuthor($author);
        $newsArticle->setUrl($url);
        $newsArticle->setSource($source);
        $newsArticle->setPublishedAt($publishedAt);
        $newsArticle->setContent($content);
        $newsArticle->setActive($active);
        return $newsArticle;
    }

    /**
     * Constructs an instance of this class from an associative array.
     * @param array $record The properties used to initialize the fields of the NewsArticle type.
     * @return NewsArticle
     * @throws Exception
     */
    public static function fromAssociativeArr(array $record = array()): NewsArticle
    {
        $newsArticle = new NewsArticle();

        foreach ($record as $key => $value) {
            if ($key == "publishedAt") {
                if (isset($value["date"])) $newsArticle->{$key} = new DateTime($value["date"]);
                else $newsArticle->{$key} = new DateTime($value);
            } else if ($key == "active" && is_int($key)) {
                $newsArticle->{$key} = $key == 1;
            } else {
                $newsArticle->{$key} = $value;
            }
        }

        return $newsArticle;
    }

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param ?int $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     */
    public function setPublishedAt(DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * Tests if a news article is equal to another. Given the volatility of the news articles, we will compare their URL's
     * and their titles considering they're the most non volatile attributes in news articles.
     * @param NewsArticle $other
     * @return bool If this news article is equals to the news article passed in the parameter.
     */
    public function equals(NewsArticle $other)
    {
        if ($this->getId() != null && $other->getId() != null)
            return $this->getId() == $other->getId() && ($this->url == $other->url || $this->title == $other->title);
        return $this->url == $other->url || $this->title == $other->title;
    }

    /**
     * Method called by the json_encode() function when serializing this object.
     * @return object
     */
    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}