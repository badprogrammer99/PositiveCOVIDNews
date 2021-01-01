<?php

namespace Storage\Interfaces;

use DateTime;
use Models\NewsArticle;

/**
 * Interface NewsStorageSystem The interface that news storage systems should implement
 * in order to store news in a local environment.
 * @package Storage\Interfaces
 */
interface NewsStorageSystem
{
    /**
     * @return array All news currently stored in the storage system.
     */
    public function getAll(): array;

    /**
     * @param int $id The ID of the news to be retrieved.
     * @return NewsArticle|null A news article if one was found, or null if none.
     */
    public function getById(int $id): NewsArticle | null;

    /**
     * @param DateTime $dateTime The date of the news to be retrieved.
     * @return NewsArticle|array|null A single news article if only one news article was found pertaining to a specific
     * date, an array of news articles if more than one news article was found, and null if none was found.
     */
    public function getByDate(DateTime $dateTime): NewsArticle | array | null;

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
     * Deletes a news article from the database.
     * @param int $id The id of the news article to be deleted.
     * @return bool True if the news article was deleted, false if not.
     * TODO: Maybe throw an exception here instead of returning a boolean?
     */
    public function delete(int $id): bool;
}