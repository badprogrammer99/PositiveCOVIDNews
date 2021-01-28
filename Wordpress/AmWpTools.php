<?php

namespace Wordpress;

use Wordpress\IXR\IXR_Client;

/**
 * Class AmWpTools Class that provides the methods to perform various WordPress task.
 * @package Wordpress
 */
class AmWpTools
{
    /**
     * @var string The WordPress admin.
     */
    private string $user;

    /**
     * @var string The password of the WordPress admin.
     */
    private string $password;

    /**
     * @var string The xmlrpc.php endpoint, to where the news are to be sent.
     */
    private string $xmlRpcLink;

    /**
     * AmWpTools constructor.
     * @param $user
     * @param $password
     * @param $xmlRpcLink
     */
    public function __construct($user, $password, $xmlRpcLink)
    {
        $this->user = $user;
        $this->password = $password;
        $this->xmlRpcLink = $xmlRpcLink;
    }

    /**
     * Posts something to a blog.
     * @param $title string The title of the post.
     * @param $body string The body of the post.
     * @param $cats string|array The categories of the post.
     * @param $keywordsString mixed The keywords of the post.
     * @param ?int $featuredImageId The featured image ID of the post.
     * @param int $allowComments Whether this post allows comments or not.
     * @param int $allowPings Whether this post allows pings or not.
     * @return bool true if the post was successfully posted, false if not
     */
    public function postToBlog(string $title, string $body, string|array $cats,
                              string $keywordsString, ?int $featuredImageId = null,
                              int $allowComments = 1, int $allowPings = 1): bool
    {
        $customfields = array(
            'key' => 'PHP PRJ',
            'value' => 'wordpresser'
        ); // Insert your custom values like this in Key, Value format
        $title = htmlentities($title, ENT_NOQUOTES, $encoding = "UTF-8");

        if ($keywordsString === "")
        {
            $keywordsString = AmWpUtils::autoGenerateKeywordsFromDescription($body);
        }

        if (gettype($cats) == "array") {
            foreach ($cats as $cat)
            {
                if (count($this->categoryExists($cat)) == 0)
                {
                    $this->createCategory($cat);
                }
            }
        } else {
            if (count($this->categoryExists($cats)) == 0)
            {
                $this->createCategory($cats);
            }
        }

        $content = null;
        if ($featuredImageId !== null)
        {
            $content = array(
                'title' => $title,
                'description' => $body,
                'mt_allow_comments' => $allowComments, // 1 to allow comments
                'mt_allow_pings' => $allowPings, // 1 to allow trackbacks
                'post_type' => 'post', //"post" or "page"
                'mt_keywords' => $keywordsString, //for example: "keyw1, keyw2"
                'categories' => $cats, //for example: "dev, testing"
                'custom_fields' => array($customfields),
                'wp_post_thumbnail' => $featuredImageId,
            );
        }
        else
        {
            $content = array(
                'title' => $title,
                'description' => $body,
                'mt_allow_comments' => $allowComments, // 1 to allow comments
                'mt_allow_pings' => $allowPings, // 1 to allow trackbacks
                'post_type' => 'post', //"post" or "page"
                'mt_keywords' => $keywordsString, //for example: "keyw1, keyw2"
                'categories' => $cats, //for example: "dev, testing"
                'custom_fields' => array($customfields),
            );
        } //else => there is no featuredImage

        // Create the client object
        $client = new IXR_Client($this->xmlRpcLink);
        $client->debug = false; //Set it to false in Production Environment
        $params = array(
            0,
            $this->user,
            $this->password,
            $content,
            true
        ); // Last parameter is 'true' which means post immediately, to save as draft set it as 'false'
        /*
        Wordpress supports the "Metaweb log api" : http://xmlrpc.scripting.com/metaWeblogApi.html
        To be able to communicate with metaWeblog.newPost, a class IXR_Library required

        ver:
        https://codex.wordpress.org/XML-RPC_MetaWeblog_API
        */
        $success = $client->query('metaWeblog.newPost', $params);

        if ($success)
        {
            $msg = '@wordpress_postToBlog : Posted OK!' . PHP_EOL;
            AmWpUtils::simplerFeedback($msg);
        }
        else
        {
            $errorCode = $client->getErrorCode();
            $errorMessage = $client->getErrorMessage();
            $msg = '@wordpress_postToBlog : Something went wrong - ' . $errorCode . ' : ' . $errorMessage . PHP_EOL;
            AmWpUtils::simplerFeedback($msg);
            return false;
        }

        return true;
    }

    /**
     * Creates a category.
     * @param $categoryName string The name of the category to be created.
     * @return mixed The response of the IXR_Client
     */
    private function createCategory(string $categoryName): mixed
    {
        $client = new IXR_Client($this->xmlRpcLink);

        $content = array(
            "name" => $categoryName,
            "description" => $categoryName,
            "parent_id" => 0,
            // https://stackoverflow.com/a/34244525
            "slug" => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $categoryName))),
        );

        $params = array(1, $this->user, $this->password, $content);

        $client->query("wp.newCategory", $params);
        return $client->getResponse();
    }

    /**
     * Checks if a category already exists.
     * @param $categoryName string The name of the category to check the existence for.
     * @return mixed The response of the IXR_Client
     */
    private function categoryExists(string $categoryName): mixed
    {
        $client = new IXR_Client($this->xmlRpcLink);

        $params = array(1, $this->user, $this->password, $categoryName, 1);

        $client->query("wp.suggestCategories", $params);
        return $client->getResponse();
    }
}