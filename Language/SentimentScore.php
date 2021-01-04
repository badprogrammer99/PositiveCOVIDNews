<?php

namespace Language;

use MyCLabs\Enum\Enum;

/**
 * @method static SentimentScore NEGATIVE()
 * @method static SentimentScore MIXED()
 * @method static SentimentScore NEUTRAL()
 * @method static SentimentScore POSITIVE()
 */
class SentimentScore extends Enum
{
    private const NEGATIVE = -2;
    private const MIXED = -1;
    private const NEUTRAL = 0;
    private const POSITIVE = 1;
}