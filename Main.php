<?php

require "vendor/autoload.php";

use DataSources\MockNewsDataSource;

define("ROOT_DIR", __DIR__);

putenv("GOOGLE_APPLICATION_CREDENTIALS=". ROOT_DIR . "/Keyfile/ACAProject-b11b07f9f94b.json");

class Main
{
    public static function main() {
        $newsDataSource = new MockNewsDataSource();
        $newsDataSource->retrieveNewsData();
    }
}

Main::main();