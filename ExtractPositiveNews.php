<?php
/*
 * MIT License
 *
 * Copyright (c) 2021 José Simões & Bruno Silva
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

require "vendor/autoload.php";

use DataSources\Abstracts\NewsDataSource;
use DataSources\CovidBBCWorldNewsDataSource;
use DataSources\CovidNewsAPIDataSource;
use DataSources\CovidRapidAPINewsDataSource;
use DataSources\MockNewsDataSource;
use Language\GoogleSentimentAnalyzer;
use Language\Interfaces\SentimentAnalyzer;
use Main\Bot;
use Main\Utils;
use Storage\Interfaces\NewsStorageSystem;
use Storage\JsonDatabaseNewsStorageSystem;
use Storage\MySQLNewsStorageSystem;

define("ROOT_DIR", __DIR__);
define("PRODUCTION", false);
ini_set("memory_limit", "1024M");
if (!PRODUCTION) error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * Class ExtractPositiveNews The class that is responsible for executing the operations defined by the user.
 */
class ExtractPositiveNews
{
    /**
     * The message that is shown when no parameters were passed.
     */
    private const NO_PARAMETERS_PASSED_MESSAGE = <<<EOD
    The script needs parameters to be passed in order to be ran. For more information on how to pass those parameters,
    please type --help.
    EOD;

    /**
     * The message that is shown when the user types the help command.
     */
    private const HELP_MESSAGE = <<<EOD
    -------------------------- USAGE --------------------------
    --newsDataSource <name of the data source to use>
    --newsStorageSystem <name of the news storage system to use>
    For a list of available news data sources and news storage systems to use, pass the --getAvailableNewsDataSources
    or --getAvailableNewsStorageSystems parameters, respectively.
    EXAMPLE USAGE: php ExtractPositiveNews.php --newsDataSource 1 --newsStorageSystem 1
    Where the --newsDataSource is the parameter that specifies from which data sources the news are to be pulled from and
    the --newsStorageSystem is the parameter that specifies where the pulled news are to be saved.
    To know which numbers to use, pass the --getAvailableNewsDataSources or --getAvailableNewsStorageSystem parameters. 
    The numbers identify the data sources/news storage systems that are able to be used.
    EOD;

    /**
     * @var array The passed command-line arguments.
     */
    private array $arguments;

    /**
     * @var NewsDataSource[] The type of news data sources that are available for the user to use.
     */
    private array $availableNewsDataSources;

    /**
     * @var NewsStorageSystem[] The type of news storage systems that are available for the user to use.
     */
    private array $availableNewsStorageSystems;

    /**
     * @var SentimentAnalyzer The sentiment analyzer to be used when analyzing the news sources. For now, we'll hardcode the
     * Google Sentiment Analyzer, since it's the only one we have available for use.
     */
    private SentimentAnalyzer $sentimentAnalyzer;

    /**
     * ExtractPositiveNews constructor. Also registers a pre-defined set of available news data sources and storage systems
     * that the user can use when calling this script from a command line.
     * @param $arguments array The command-line arguments
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
        $this->sentimentAnalyzer = new GoogleSentimentAnalyzer();
        $this->registerAvailableNewsDataSources();
        $this->registerAvailableNewsStorageSystems();
        $this->interpretUserArguments();
    }

    /**
     * Runs the bot with the interpreted user arguments.
     * @param $interpretedUserArguments
     */
    public function run($interpretedUserArguments)
    {
        $bot = Bot::createBot($interpretedUserArguments["newsDataSourcesToBeUsed"],
            $interpretedUserArguments["newsStorageSystemsToBeUsed"],
            $this->sentimentAnalyzer);
        $bot->setDelay(2);
        $bot->run();
    }

    /**
     * Registers the available news data sources for the user to see when he first executes this script.
     */
    private function registerAvailableNewsDataSources()
    {
        $this->availableNewsDataSources = array(
            new CovidNewsAPIDataSource(),
            new CovidRapidAPINewsDataSource(),
            new CovidBBCWorldNewsDataSource(),
            new MockNewsDataSource()
        );
    }

    /**
     * Registers the available news storage systems for the user to see when he first executes this script.
     */
    private function registerAvailableNewsStorageSystems()
    {
        $this->availableNewsStorageSystems = array(
            new JsonDatabaseNewsStorageSystem(),
            new MySQLNewsStorageSystem()
        );
    }

    /**
     * Interprets the user arguments.
     * The possible commands are:
     * --help: Gets basic help for the commands that can be ran with this script.
     * --getAvailableNewsDataSources: Gets the list of available news data sources for news retrieval.
     * --getAvailableNewsStorageSystems: Gets the list of available news storage systems for saving the news.
     * --newsDataSources: Specifies the data sources from which the bot should retrieve news from.
     * --newsStorageSystems: Specifies the storage systems that this bot should use to save news. This command is intended
     * to be used in conjunction with the --newsDataSources command.
     * If any of the pre-requisites are not met, the script will halt execution.
     */
    private function interpretUserArguments()
    {
        if (count($this->arguments) == 0) {
            die(self::NO_PARAMETERS_PASSED_MESSAGE);
        }

        if (array_key_exists("help", $this->arguments)) {
            die(self::HELP_MESSAGE);
        }

        if (array_key_exists("getAvailableNewsDataSources", $this->arguments)) {
            echo "-------------------------- AVAILABLE NEWS DATA SOURCES --------------------------\n";

            for ($i = 0; $i < count($this->availableNewsDataSources); $i++) {
                echo $i + 1 . " - " . $this->availableNewsDataSources[$i]->getUserFriendlyDataSourceName() . "\n";
            }

            die("When selecting the data sources from which to pull news from, choose them by their respective number.");
        }

        if (array_key_exists("getAvailableNewsStorageSystems", $this->arguments)) {
            echo "-------------------------- AVAILABLE NEWS STORAGE SYSTEMS --------------------------\n";

            for ($i = 0; $i < count($this->availableNewsStorageSystems); $i++) {
                echo $i + 1 . " - " . $this->availableNewsStorageSystems[$i]->getUserFriendlyNewsStorageName() . "\n";
            }

            die("When selecting the news storage system to save news, choose them by their respective number.");
        }

        if (array_key_exists("newsDataSources", $this->arguments)) {
            if (!array_key_exists("newsStorageSystems", $this->arguments))
                die("You need to specify the news storage system to use alongside the chosen data source(s). You can do
                that with the --newsStorageSystem command. For more information, type --help.");

            $userArguments = array(
                "newsDataSourcesToBeUsed" => [],
                "newsStorageSystemsToBeUsed" => []
            );

            $newsDataSourcePositions = Utils::parseArgument($this->arguments["newsDataSources"]);
            foreach ($newsDataSourcePositions as $newsDataSourcePos) {
                $newsDataSource = $this->getAvailableNewsDataSourceByPos($newsDataSourcePos - 1);
                if ($newsDataSource != null) {
                    $userArguments["newsDataSourcesToBeUsed"][] = $newsDataSource;
                } else {
                    die("The data source number " . $newsDataSourcePos . " does not exist.");
                }
            }

            $newsStorageSystemPositions = Utils::parseArgument($this->arguments["newsStorageSystems"]);
            foreach ($newsStorageSystemPositions as $newsStorageSystemPos) {
                $newsStorageSystem = $this->getAvailableNewsStorageSystemByPos($newsStorageSystemPos - 1);
                if ($newsStorageSystem != null) {
                    $userArguments["newsStorageSystemsToBeUsed"][] = $newsStorageSystem;
                } else {
                    die("The news storage system number " . $newsStorageSystemPos . " does not exist.");
                }
            }

            $this->run($userArguments);
        }
    }

    /**
     * Gets an available news data source by position.
     * @param $newsDataSourcePos
     * @return NewsDataSource|null A NewsDataSource object if anything was found, or null if otherwise
     */
    private function getAvailableNewsDataSourceByPos($newsDataSourcePos): ?NewsDataSource
    {
        if (!isset($this->availableNewsDataSources[$newsDataSourcePos])) return null;
        return $this->availableNewsDataSources[$newsDataSourcePos];
    }

    /**
     * Gets an available news storage system by position.
     * @param $newsStorageSystemPos
     * @return NewsStorageSystem|null A NewsStorageSystem object if anything was found, or null if otherwise
     */
    private function getAvailableNewsStorageSystemByPos($newsStorageSystemPos): ?NewsStorageSystem
    {
        if (!isset($this->availableNewsStorageSystems[$newsStorageSystemPos])) return null;
        return $this->availableNewsStorageSystems[$newsStorageSystemPos];
    }
}

$positiveNewsExtractor = new ExtractPositiveNews(getopt(
    "", array("help",
    "getAvailableNewsDataSources",
    "getAvailableNewsStorageSystems",
    "newsDataSources:",
    "newsStorageSystems:")
));