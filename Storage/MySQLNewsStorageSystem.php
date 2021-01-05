<?php

namespace Storage;

use DateTime;
use Exceptions\NotImplementedException;
use Models\DatabaseCredentials;
use Models\NewsArticle;
use mysqli;
use Storage\Interfaces\NewsStorageSystem;
use RuntimeException;

/**
 * Class MySQLNewsStorageSystem Implements a news storage system using the MySQL relational database.
 * @author Bruno Silva
 * @package Storage
 */

class MySQLNewsStorageSystem implements NewsStorageSystem
{
    /**
     * The hardcoded credentials of our database host.
     */
    private const DEFAULT_HOST = "localhost";
    private const DEFAULT_USER = "root";
    private const DEFAULT_PASS = "";
    private const DEFAULT_PORT = 3306;

    /**
     * String to create our MySQL database schema.
     */
    private const CREATE_SCHEMA_SCRAPED_NEWS_MEMORY = "CREATE SCHEMA IF NOT EXISTS ACA;";

    /**
     * String to create the table which will hold our scraped news.
     */
    private const CREATE_SCRAPED_NEWS_TABLE = "-- noinspection SqlDialectInspection
        CREATE TABLE IF NOT EXISTS ACA.News(
        _id INT NOT NULL AUTO_INCREMENT,
        title TEXT NOT NULL,
        author TEXT NOT NULL,
        url TEXT NOT NULL,
        published_at DATETIME NOT NULL,
        content TEXT NOT NULL,
        PRIMARY KEY(_id));
    ";

    /**
     * @var DatabaseCredentials The class which will hold the credentials of our database.
     */
    private DatabaseCredentials $databaseCredentials;

    /**
     * @var mysqli The instance of our database.
     */
    private mysqli $db;

    /**
     * MySQLNewsStorageSystem constructor.
     */
    public function __construct(){
        $this->databaseCredentials = new DatabaseCredentials(self::DEFAULT_HOST,
            self::DEFAULT_USER,
            self::DEFAULT_PASS,
            self::DEFAULT_PORT
        );
    }

    /**
     * Sets up our DB (connects to the database host and then proceeds to create the schemas and tables if they are not
     * created yet). Also sets up mysqli to throw on every MySQL error that could happen.
     */
    public function setupDb() {
        // throw on every mysqli error that could possibly happen
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->db = mysqli_connect($this->databaseCredentials->getHost(),
            $this->databaseCredentials->getUser(),
            $this->databaseCredentials->getPass(),
            "",
            $this->databaseCredentials->getPort()
        );

        $this->db->query(self::CREATE_SCHEMA_SCRAPED_NEWS_MEMORY);
        $this->db->query(self::CREATE_SCRAPED_NEWS_TABLE);
    }

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
    function getById(int $id): NewsArticle | null
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