<?php

namespace Main;

use DataSources\Abstracts\NewsDataSource;
use RuntimeException;
use Storage\Interfaces\NewsStorageSystem;

/**
 * Class Utils A class of utility methods to be used throughout the different classes of this project.
 */
class Utils
{
    /**
     * Check if the passed object parameter is a news data source instance.
     * @param mixed $obj
     */
    public static function checkIfObjIsNewsDataSourceInstance(mixed $obj)
    {
        if (!$obj instanceof NewsDataSource)
            throw new RuntimeException("Object is not a NewsDataSource instance!");
    }

    /**
     * Check if the passed object parameter is a news storage system instance.
     * @param mixed $obj
     */
    public static function checkIfObjIsNewsStorageSystemInstance(mixed $obj)
    {
        if (!$obj instanceof NewsStorageSystem)
            throw new RuntimeException("Object is not a NewsStorageSystem instance!");
    }


    /**
     * Check if the array only has objects composed of news data source instances.
     * @param array $arr
     */
    public static function checkIfArrayIsComposedOfNewsDataSourceInstances(array $arr)
    {
        foreach ($arr as $value) {
            self::checkIfObjIsNewsDataSourceInstance($value);
        }
    }

    /**
     * Check if the array only has objects composed of news storage system instances.
     * @param array $arr
     */
    public static function checkIfArrayIsComposedOfNewsStorageSystemInstances(array $arr)
    {
        foreach ($arr as $value) {
            self::checkIfObjIsNewsStorageSystemInstance($value);
        }
    }

    /**
     * Converts an object to an associative array.
     * @param $obj mixed The object to be converted to an associative array.
     * @return mixed
     */
    public static function convertObjToAssociativeArr(mixed $obj): mixed
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * Converts all objects inside of the passed array to associative arrays and returns the array with the
     * converted objects.
     * @param array $arr The array with the non-converted objects.
     * @return array
     */
    public static function convertAllObjsInsideArrToAssociativeArrs(array $arr): array
    {
        $newArr = array();

        foreach ($arr as $obj) {
            $newArr[] = self::convertObjToAssociativeArr($obj);
        }

        return $newArr;
    }

    /**
     * Parses a command line argument.
     * @param $arg
     * @return array The parsed command line argument.
     */
    public static function parseArgument($arg): array
    {
        if (str_contains($arg, ",")) {
            return explode(",", $arg);
        }

        return array($arg);
    }
}

