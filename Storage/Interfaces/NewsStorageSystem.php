<?php

namespace Storage\Interfaces;

use DateTime;
use Exception;
use Models\NewsArticle;

/**
 * Interface NewsStorageSystem The interface that news storage systems should implement
 * in order to store news in a local environment.
 * @package Storage\Interfaces
 */
interface NewsStorageSystem
{
    /**
     * @return NewsArticle[] All news currently stored in the storage system.
     */
    public function getAll(): array;

    /**
     * @param int $id The ID of the news to be retrieved.
     * @return NewsArticle|null A news article if one was found, or null if none.
     */
    public function getById(int $id): NewsArticle | null;

    /**
     * @param DateTime $dateTime The date of the news to be retrieved.
     * @return array|null A single news article if only one news article was found pertaining to a specific
     * date, an array of news articles if more than one news article was found, and null if none was found.
     */
    public function getByDate(DateTime $dateTime): array | null;

    /**
     * @param string $title The title of the news to be retrieved.
     * @return array|null An array of news articles, and null if no news article was found with the passed title.
     */
    public function getByTitle(string $title): array | null;

    /**
     * Gets the ID of the record who was inserted the latest.
     * @return int
     */
    public function getLastInsertedId(): int;

    /**
     * Inserts a new news article into the database.
     * @param NewsArticle $newsArticle The news article to be inserted.
     */
    public function insert(NewsArticle $newsArticle): void;

    /**
     * Inserts a new batch of news articles into the database.
     * @param array $newsArticles The batch of news articles to be inserted.
     */
    public function insertAll(array $newsArticles): void;

    /**
     * Updates a news article.
     * @param NewsArticle $newsArticle
     */
    public function update(NewsArticle $newsArticle): void;

    /**
     * Deletes a news article from the database.
     * @param int $id The id of the news article to be deleted.
     * @throws Exception If the article couldn't be deleted.
     */
    public function delete(int $id): void;

    /**
     * Marks a news article as active.
     * @param int $id The ID of the news article to be marked as active.
     */
    public function markNewsArticleAsActive(int $id): void;

    /**
     * Marks a news article as inactive.
     * @param int $id The ID of the news article to be marked as not active.
     */
    public function markNewsArticleAsInactive(int $id): void;

    /**
     * Gets the user friendly name of the storage system.
     * @return string The user friendly name of the storage system.
     */
    public function getUserFriendlyNewsStorageName(): string;
}