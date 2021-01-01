<?php

use DataSources\Abstracts\NewsDataSource;

/**
 * Class Utils A class of utility methods to be used throughout the different classes of this project.
 */
class Utils
{
    /**
     * Check if the passed object parameter is a news data source instance.
     * @param $obj
     */
    public static function checkIfObjIsNewsDataSourceInstance($obj) {
        if (!$obj instanceof NewsDataSource)
            throw new RuntimeException("Object is not a NewsDataSource instance!");
    }
}