<?php /** @noinspection SqlNoDataSourceInspection */

namespace Storage;

use DateTime;
use Exception;
use Models\NewsArticle;
use mysqli;
use stdClass;
use Storage\Interfaces\NewsStorageSystem;

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
        id INT NOT NULL AUTO_INCREMENT,
        title TEXT NOT NULL,
        author TEXT NOT NULL,
        url TEXT NOT NULL,
        source TEXT NOT NULL,
        publishedAt DATETIME NOT NULL,
        content TEXT NOT NULL,
        active BOOLEAN NOT NULL,
        PRIMARY KEY(id));
    ";

    /**
     * @var mysqli The instance of our database.
     */
    private mysqli $db;

    /**
     * Sets up our DB (connects to the database host and then proceeds to create the schemas and tables if they are not
     * created yet). Also sets up mysqli to throw on every MySQL error that could happen.
     */
    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR);

        $this->db = mysqli_connect(self::DEFAULT_HOST,
            self::DEFAULT_USER,
            self::DEFAULT_PASS,
            null,
            self::DEFAULT_PORT
        );

        $this->db->query(self::CREATE_SCHEMA_SCRAPED_NEWS_MEMORY);
        $this->db->query(self::CREATE_SCRAPED_NEWS_TABLE);
        mysqli_set_charset($this->db, "utf8");
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function getAll(): array
    {
        $query = "SELECT * FROM ACA.news";
        $results = mysqli_fetch_all($this->db->query($query), MYSQLI_ASSOC);

        $newsArticles = [];
        foreach ($results as $result) {
            $newsArticles[] = NewsArticle::fromAssociativeArr($result);
        }

        return $newsArticles;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function getById(int $id): NewsArticle | null
    {
        $query = "SELECT * FROM ACA.news WHERE id=$id";
        $result = $this->db->query($query)->fetch_assoc();

        if ($result != null) {
            return NewsArticle::fromAssociativeArr($result);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function getByDate(DateTime $dateTime): array | null
    {
        $formattedDate = $dateTime->format("Y-m-d");
        $query = "SELECT * FROM ACA.news WHERE publishedAt='$formattedDate'";
        $results = $this->db->query($query);

        $newsArticles = [];
        while ($result = $results->fetch_assoc()) {
            $newsArticles[] = NewsArticle::fromAssociativeArr($result);
        }

        return count($newsArticles) > 0 ? $newsArticles : null;
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function getByTitle(string $title): array | null
    {
        $escapedTitle = mysqli_real_escape_string($this->db, $title);
        $query = "SELECT * FROM ACA.news WHERE title='$escapedTitle'";
        $results = $this->db->query($query);

        $newsArticles = [];
        while ($result = $results->fetch_assoc()) {
            $newsArticles[] = NewsArticle::fromAssociativeArr($result);
        }

        return count($newsArticles) > 0 ? $newsArticles : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastInsertedId(): int
    {
        return mysqli_insert_id($this->db) + 1;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(NewsArticle $newsArticle): void
    {
        $newsArticle = $this->escapeNewsArticle($newsArticle);

        $query = "INSERT INTO ACA.news VALUES (
             " . $newsArticle->id . ",
             " . "\"$newsArticle->title\"" . ",
             " . "\"$newsArticle->author\"" . ",
             " . "\"$newsArticle->url . \"" . ",
             " . "\"$newsArticle->source\"" . ",
             " . "\"$newsArticle->publishedAt\"" . ",
             " . "\"$newsArticle->content\"" . ",
             " . $newsArticle->active . ")";

        $this->db->query($query);
    }

    /**
     * {@inheritDoc}
     */
    public function insertAll(array $newsArticles): void
    {
        foreach ($newsArticles as $newsArticle) {
            $this->insert($newsArticle);
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(NewsArticle $newsArticle): void
    {
        if ($this->getById($newsArticle->getId()) !== null) {
            $newsArticle = $this->escapeNewsArticle($newsArticle);

            $query = "UPDATE ACA.news
            SET title=" . "\"$newsArticle->title\"" . ",
            author=" . "\"$newsArticle->author\"" . ",
            url=" . "\"$newsArticle->url\"" . ",
            source=" . "\"$newsArticle->source\"" . ",
            publishedAt=" . "\"$newsArticle->publishedAt\"" . ",
            content=" . "\"$newsArticle->content\"" . ",
            active=$newsArticle->active WHERE id=$newsArticle->id";

            $this->db->query($query);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): void
    {
        $query = "DELETE * FROM ACA.news WHERE id=$id";
        $this->db->query($query);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function markNewsArticleAsActive(int $id): void
    {
        $record = $this->getById($id);
        $record->setActive(true);
        $this->update($record);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function markNewsArticleAsInactive(int $id): void
    {
        $record = $this->getById($id);
        $record->setActive(false);
        $this->update($record);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserFriendlyNewsStorageName(): string
    {
        return "MySQL database";
    }

    /**
     * Escapes the news article before sending it to the database.
     * @param NewsArticle $newsArticle The news article to be escaped.
     * @return stdClass The escaped news article.
     */
    private function escapeNewsArticle(NewsArticle $newsArticle): stdClass
    {
        $escapedNewsArticle = new stdClass();

        $escapedNewsArticle->id = $newsArticle->getId() === null ? 'null' : $newsArticle->getId();
        $escapedNewsArticle->title = mysqli_real_escape_string($this->db, $newsArticle->getTitle());
        $escapedNewsArticle->author = mysqli_real_escape_string($this->db, $newsArticle->getAuthor());
        $escapedNewsArticle->url = $newsArticle->getUrl();
        $escapedNewsArticle->source = mysqli_real_escape_string($this->db, $newsArticle->getSource());
        $escapedNewsArticle->publishedAt = $newsArticle->getPublishedAt()->format("Y-m-d");
        $escapedNewsArticle->content = mysqli_real_escape_string($this->db, $newsArticle->getContent());
        $escapedNewsArticle->active = $newsArticle->isActive() ? 1 : 0;

        return $escapedNewsArticle;
    }
}