<?php

namespace Storage;

use ArrayAccess;
use DateTime;
use Exception;
use Exceptions\NotImplementedException;
use Main\Utils;
use Models\NewsArticle;
use SleekDB\SleekDB;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class JsonDatabaseNewsStorageSystem Implements a news storage system using the SleekDB NoSQL JSON database.
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
    public function __construct() {
        $this->sleekDb = SleekDB::store("news", self::$JSON_DB_DIR);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return $this->sleekDb->fetch();
    }

    /**
     * @inheritDoc
     * @throws Exception If the preconditions of the where clause weren't fulfilled
     */
    public function getById(int $id): NewsArticle | null
    {
        return $this->sleekDb
            ->where("_id", "=", $id)
            ->fetch();
    }

    /**
     * @inheritDoc
     */
    public function getByDate(DateTime $dateTime): NewsArticle|array|null
    {
        // TODO: Implement getByDate() method.
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws Exception If no data has been found to store or if the datatype is not an array or doesn't implement
     * the @var ArrayAccess interface
     */
    public function insert(NewsArticle $newsArticle): void
    {
        $convertedNewsArticle = Utils::convertObjToAssociativeArr($newsArticle);
        $this->sleekDb->insert($convertedNewsArticle);
    }

    /**
     * @inheritDoc
     * @throws Exception If no data has been found to store or if the datatype is not an array
     */
    public function insertAll(array $newsArticles): void
    {
        $convertedNewsArticles = Utils::convertAllObjsInsideArrToAssociativeArrs($newsArticles);
        $this->sleekDb->insertMany($convertedNewsArticles);
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
}