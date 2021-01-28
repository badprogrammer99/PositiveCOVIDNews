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

use Main\Utils;
use Storage\Interfaces\NewsStorageSystem;
use Storage\JsonDatabaseNewsStorageSystem;
use Storage\MySQLNewsStorageSystem;
use Wordpress\AmWpTools;
use Wordpress\AmWpUtils;

require "vendor/autoload.php";

define("ROOT_DIR", __DIR__);
define("PRODUCTION", false);
ini_set("memory_limit", "1024M");
if (!PRODUCTION) error_reporting(E_ALL ^ E_DEPRECATED);

class PublishNewsToWpBlog
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
    --newsStorageSystems <names of the news storage system to retrieve news from>
    --user <the user of the WordPress blog>
    --pass <the pass of the WordPress blog>
    --xmlRpcEndpoint <the XML Rpc endpoint of the Wordpress blog>
    For a list of available news storage systems to use, type --getAvailableNewsStorageSystems
    EXAMPLE USAGE: php PublishNewsToWpBlog.php --newsStorageSystem 1 --user admin --pass trabalhoaca 
    --xmlRpcEndpoint http://localhost/positivenews/wordpress/xmlrpc.php
    Where the --newsStorageSystem argument specifies from which news storage system are news to be pulled from and the --user,
    --pass, and --xmlRpcEndpoints arguments specify to where the retrieved news are to be sent for.
    To know which numbers to use, pass the --getAvailableNewsStorageSystem parameter. 
    The numbers identify the news storage systems that are able to be used.
    EOD;

    /**
     * @var array The passed command-line arguments.
     */
    private array $arguments;

    /**
     * @var NewsStorageSystem[] The type of news storage systems that are available for the user to use.
     */
    private array $availableNewsStorageSystems;

    /**
     * PublishNewsToWpBlog constructor.
     * @param $arguments array The command-line arguments.
     */
    public function __construct($arguments)
    {
        $this->arguments = $arguments;
        $this->registerAvailableNewsStorageSystems();
        $this->interpretUserArguments();
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
     * --getAvailableNewsStorageSystems: Gets the list of available news storage systems to retrieve news from.
     * --newsStorageSystems: Specifies the storage systems to retrieve news from.
     * --user: Specifies the WordPress admin user.
     * --pass: Specifies the WordPress admin password.
     * --xmlRpcEndpoint: Specifies the XML RPC endpoint (to where the post data is to be sent).
     * If any of the pre-requisites are not met, the script will halt execution.
     */
    private function interpretUserArguments()
    {
        if (count($this->arguments) == 0) {
            die(self::NO_PARAMETERS_PASSED_MESSAGE);
        }

        if ($this->checkIfCmdLineArgIsPresent("help")) {
            die(self::HELP_MESSAGE);
        }

        if ($this->checkIfCmdLineArgIsPresent("getAvailableNewsStorageSystems")) {
            echo "-------------------------- AVAILABLE NEWS STORAGE SYSTEMS --------------------------\n";

            for ($i = 0; $i < count($this->availableNewsStorageSystems); $i++) {
                echo $i + 1 . " - " . $this->availableNewsStorageSystems[$i]->getUserFriendlyNewsStorageName() . "\n";
            }

            die("When selecting the news storage system to retrieve news from, choose them by their respective number.");
        }

        if ($this->checkIfCmdLineArgHasValue("newsStorageSystems")) {
            if ($this->checkIfCmdLineArgHasValue("user")
                && $this->checkIfCmdLineArgHasValue("pass")
                && $this->checkIfCmdLineArgHasValue("xmlRpcEndpoint"))
            {
                $newsArticlesToUse = [];
                $newsStorageSystemsToUse = [];

                $newsStorageSystemNumbers = Utils::parseArgument($this->arguments["newsStorageSystems"]);
                foreach ($newsStorageSystemNumbers as $newsStorageSystemNumber) {
                    $newsStorageSystem = $this->getAvailableNewsStorageSystemByPos($newsStorageSystemNumber - 1);
                    if ($newsStorageSystem != null) {
                        $newsStorageSystemName = $newsStorageSystem->getUserFriendlyNewsStorageName();
                        $newsArticlesToUse[$newsStorageSystemName] = $newsStorageSystem->getAll();
                        $newsStorageSystemsToUse[$newsStorageSystemName] = $newsStorageSystem;
                    }
                }

                $user = Utils::parseArgument($this->arguments["user"])[0];
                $pass = Utils::parseArgument($this->arguments["pass"])[0];
                $xmlRpcEndpoint = Utils::parseArgument($this->arguments["xmlRpcEndpoint"])[0];

                $amWpTools = new AmWpTools($user, $pass, $xmlRpcEndpoint);

                foreach ($newsArticlesToUse as $newsStorageSystemName => $newsArticles) {
                    foreach ($newsArticles as $newsArticle) {
                        if (!$newsArticle->isActive()) {
                            $wasNewsPostedToBlog = $amWpTools->postToBlog(
                                $newsArticle->getTitle(),
                                $body = $newsArticle->getContent(),
                                $cats = array($newsArticle->getSource()),
                                $keywordsString = AmWpUtils::autoGenerateKeywordsFromDescription($newsArticle->getContent()),
                                $featuredImageId = null,
                                $allowComments = true,
                                $allowPings = true);

                            if ($wasNewsPostedToBlog) {
                                $newsStorageSystemsToUse[$newsStorageSystemName]->markNewsArticleAsActive($newsArticle->getId());
                            }
                        }
                    }
                }


            } else {
                die("Please provide the WordPress user and login, along with a XML RPC endpoint to post the news to");
            }
        }
    }

    /**
     * Check if a command line argument is present.
     * @param $arg mixed The command line argument.
     * @return bool true if the argument is present, false if otherwise.
     */
    private function checkIfCmdLineArgIsPresent($arg): bool
    {
        return array_key_exists($arg, $this->arguments);
    }

    /**
     * Check if a command line argument has a value other than false.
     * @param $arg mixed The command line argument.
     * @return bool true if the argument has a value other than false, false if otherwise.
     */
    private function checkIfCmdLineArgHasValue($arg): bool
    {
        return $this->checkIfCmdLineArgIsPresent($arg) && $this->arguments[$arg] !== false;
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

$publishNewsToWpBlog = new PublishNewsToWpBlog(getopt(
    "", array("help",
        "getAvailableNewsStorageSystems",
        "newsStorageSystems:",
        "user:",
        "pass:",
        "xmlRpcEndpoint:"
    )
));