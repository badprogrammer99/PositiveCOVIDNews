<?php

namespace Storage;

use DateTime;
use Exceptions\NotImplementedException;
use Models\NewsArticle;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class MySQLNewsStorageSystem Implements a news storage system using the MySQL relational database.
 * To be implemented by my colleague
 * @author Bruno Silva
 * @package Storage
 */
class MySQLNewsStorageSystem implements NewsStorageSystem
{
    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        // TODO: Implement getAll() method.
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     */
    public function getById(int $id): NewsArticle | null
    {
        // TODO: Implement getById() method.
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     */
    public function getByDate(DateTime $dateTime): NewsArticle | array | null
    {
        // TODO: Implement getByDate() method.
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     */
    public function insert(NewsArticle $newsArticle): void
    {
        // TODO: Implement insert() method.
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     */
    public function insertAll(array $newsArticles): void
    {
        // TODO: Implement insertAll() method.
        throw new NotImplementedException();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
        throw new NotImplementedException();
    }
}