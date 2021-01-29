<?php

namespace Wordpress;

use Exception;

/**
 * Class AmWpUtils Class of utility functions to be used in the main Wordpress class.
 * Most of these functions are authored by @author Artur Marques.
 * @package Wordpress
 */
class AmWpUtils
{
    /**
     * Provides a simpler feedback message.
     * @param mixed $m A message
     * @return string|string[]
     * @author Artur Marques
     */
    public static function simplerFeedback($m)
    {
        $sapiName = php_sapi_name();
        //echo "sapiName = $sapiName  ";

        $CONSOLE=( $sapiName === 'cli' );

        if (!$CONSOLE){
            $m=str_replace("\n", "<br>", $m);
        }
        else{
            //$m=str_replace("<mark>", "** ", $m);
            //$m=str_replace("</mark>", " **", $m);
            $m=str_replace("<br>", PHP_EOL, $m);
            $m=str_replace("<hr>", "----------------------------------------------------\n", $m); //20140902
            $m=str_replace("\n", PHP_EOL, $m);
        }

        //http://stackoverflow.com/questions/3133209/how-to-flush-output-after-each-echo-call
        try{
            @ob_end_flush();

            //while (@ob_end_flush()); //flush all output buffers
        }
        catch (Exception $e){
            $mError=$e->getMessage();
            echo "Exception $mError while exec'ing \"ob_end_flush();\"".PHP_EOL;
        }

        # CODE THAT NEEDS IMMEDIATE FLUSHING
        echo $m;

        try{
            ob_start();
        }
        catch (Exception $e){
            $mError=$e->getMessage();
            echo "Exception $mError while exec'ing \"ob_start();\"".PHP_EOL;
        }

        return $m;
    }//simplerFeedback

    /**
     * Auto-generates keywords from a description.
     * @param string $desc The description.
     * @param int $maxKeywords The maximum number of keywords to be generated.
     * @param int $minKeywordLength The minimum length of the keywords to be generated.
     * @return string The generated keywords.
     */
    public static function autoGenerateKeywordsFromDescription(string $desc, int $maxKeywords = 8, int $minKeywordLength = 2)
    {
        $keywordsString = "";
        $keywords = array();
        $desc = trim($desc);

        $wordsInDesc = explode(" ", $desc);

        $limit = count($wordsInDesc) >= $maxKeywords ? $maxKeywords : count($wordsInDesc);

        for ($word = 0; $word < $limit; $word++) {
            $currentWord = $wordsInDesc[$word];
            $sanitizedWord = AmWpUtils::sanitizeSymbolSequenceToLettersOnly($currentWord);
            if (strlen($sanitizedWord) >= $minKeywordLength) $keywords[] = $sanitizedWord;
        } //for
        $howManyKeywords = count($keywords);
        for ($wordIdx = 0; $wordIdx < $howManyKeywords; $wordIdx++) {
            $currentKeyword = $keywords[$wordIdx];
            if ($wordIdx !== $howManyKeywords - 1) {
                $keywordsString .= $currentKeyword . ", ";
            } else {
                $keywordsString .= $currentKeyword;
            }
        } //for
        return $keywordsString;
    }

    /**
     * NOT TESTED!
     * @param string $seq
     * @return string
     * @author Artur Marques
     */
    public static function sanitizeSymbolSequenceToLettersOnly(string $seq)
    {
        $ret="";
        $size=strlen($seq);
        for ($f=0; $f<$size; $f++){
            $symbol=$seq[$f];
            $small=$symbol>="a" && $symbol<="z";
            $caps=$symbol>="A" && $symbol<="Z";
            if ($small || $caps) $ret.=$symbol;
        }//for
        return $ret;
    }//util_sanitize_symbol_sequence_to_letters_only
}