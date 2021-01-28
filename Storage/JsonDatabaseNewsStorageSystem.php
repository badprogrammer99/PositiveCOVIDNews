<?php

namespace Storage;

use DateTime;
use Exception;
use Exceptions\NotImplementedException;
use Main\Utils;
use Models\NewsArticle;
use SleekDB\SleekDB;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class JsonDatabaseNewsStorageSystem Implements a news storage system using the SleekDB NoSQL JSON database.
 * @author Bruno Silva
 * @package Storage
 */
class JsonDatabaseNewsStorageSystem implements NewsStorageSystem
{
    /**
     * @var string The location to where we should write records into.
     */
    private static string $JSON_DB_DIR = ROOT_DIR . "/Mocks";

    /**
     * @var SleekDB The instance of the Sleek DB (aka our JSON database).
     */
    private SleekDB $sleekDb;

    /**
     * JsonDatabaseNewsStorageSystem constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->sleekDb = SleekDB::store("news", self::$JSON_DB_DIR);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAll(): array
    {
        $records = $this->sleekDb->fetch();
        $convertedRecords = [];

        foreach ($records as $record) {
            $convertedRecords[] = NewsArticle::fromAssociativeArr($record);
        }

        return $convertedRecords;
    }

    /**
     * @inheritDoc
     * @throws Exception If the preconditions of the where clause weren't fulfilled
     */
    public function getById(int $id): NewsArticle | null
    {
        $record = $this->sleekDb
            ->where("_id", "=", $id)
            ->fetch();

        if ($record !== null) {
            return NewsArticle::fromAssociativeArr($record[0]);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getByDate(DateTime $dateTime): NewsArticle | array | null
    {
        // not implemented for now. throws everytime it's called. it's hard to retrieve something by datetime
        // in a json file. figure out a way
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getLastInsertedId(): int
    {
        $counterPath = self::$JSON_DB_DIR. '/news/_cnt.sdb';
        $id = 0;

        if (file_exists($counterPath)) {
            $id = (int) file_get_contents($counterPath);
        }

        return $id + 1;
    }

    /**
     * @inheritDoc
     * @throws Exception If no data has been found to store or if the datatype is not an array
     */
    public function insert(NewsArticle $newsArticle): void
    {
        $newsArticle->setId($this->getLastInsertedId());
        $convertedNewsArticle = Utils::convertObjToAssociativeArr($newsArticle);
        $this->sleekDb->insert($convertedNewsArticle);
    }

    /**
     * @inheritDoc
     * @throws Exception If no data has been found to store or if the datatype is not an array
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
            $this->sleekDb->update(Utils::convertObjToAssociativeArr($newsArticle));
        }
    }

    /**
     * @inheritDoc
     * @throws Exception If the preconditions of the where clause weren't fulfilled or the delete operation failed
     */
    public function delete(int $id): void
    {
        $this->sleekDb
            ->where("_id", "=", $id)
            ->delete();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function markNewsArticleAsActive(int $id): void
    {
        $newsArticle = $this->getById($id);
        $newsArticle->setActive(true);
        $this->update($newsArticle);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function markNewsArticleAsInactive(int $id): void
    {
        $newsArticle = $this->getById($id);
        $newsArticle->setActive(false);
        $this->update($newsArticle);
    }

    /**
     * @inheritDoc
     */
    public function getUserFriendlyNewsStorageName(): string
    {
        return "Local JSON database";
    }
}