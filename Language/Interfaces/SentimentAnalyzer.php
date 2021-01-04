<?php

namespace Language\Interfaces;

use Language\SentimentScore;

/**
 * Interface SentimentAnalyzer An interface that defines the general contract for sentiment analyzers.
 * @package Language\Interfaces
 */
interface SentimentAnalyzer
{
    /**
     * @param string $text Gets the overall sentiment for a text.
     * @return SentimentScore The obtained sentiment score. It can be either negative (the text is negative on its content),
     * neutral (the text is neutral or has mixed emotions), or positive (the text is positive).
     */
    public function getSentimentForText(string $text): SentimentScore;
}