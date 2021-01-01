<?php

namespace Parsers\Interfaces;

/**
 * Interface NewsParser Interface that defines a parser that parses the news data from an external datasource.
 * @package Parsers\Interfaces
 */
interface NewsParser
{
    /**
     * Parse the news data from the external datasource.
     * @param $newsData
     * @return array An array of parsed news articles.
     */
    public function parseNewsData($newsData): array;
}