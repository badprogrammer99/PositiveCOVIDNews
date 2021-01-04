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
     * @var DateTime Time at which the news article was published.
     */
    private DateTime $publishedAt;

    /**
     * @var string Content of the news article.
     */
    private string $content;

    /**
     * NewsArticle constructor.
     * @param array $properties
     * @throws Exception
     */
    public function __construct(array $properties = array()) {
        foreach ($properties as $key => $value) {
            if ($key == "publishedAt") {
                $this->{$key} = new DateTime($value["date"]);
            } else {
                $this->{$key} = $value;
            }
        }
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
     * Method called by the json_encode() function when serializing this object.
     * @return object
     */
    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}