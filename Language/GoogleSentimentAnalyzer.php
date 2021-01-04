<?php

namespace Language;

use Google\ApiCore\ApiException;
use Google\Cloud\Language\V1\Document;
use Google\Cloud\Language\V1\Document\Type;
use Google\Cloud\Language\V1\LanguageServiceClient;
use Language\Interfaces\SentimentAnalyzer;

putenv("GOOGLE_APPLICATION_CREDENTIALS=". ROOT_DIR . "/Keyfile/ACAProject-b11b07f9f94b.json");

class GoogleSentimentAnalyzer implements SentimentAnalyzer
{
    /**
     * @var LanguageServiceClient The Google language service client.
     */
    private LanguageServiceClient $languageServiceClient;

    /**
     * GoogleSentimentAnalyzer constructor.
     */
    public function __construct()
    {
        $this->languageServiceClient = new LanguageServiceClient();
    }

    /**
     * Gets the sentiment/emotion of a text
     * @param string $text The text to get the sentiment/emotion for
     * @return SentimentScore The overall sentiment score of the text
     * @throws ApiException If a HTTP/API error occurs while performing the API request
     */
    public function getSentimentForText(string $text): SentimentScore
    {
        $document = (new Document())
            ->setContent($text)
            ->setType(Type::PLAIN_TEXT);

        $response = $this->languageServiceClient->analyzeSentiment($document);
        $documentSentiment = $response->getDocumentSentiment();
        $score = $documentSentiment->getScore();

        if ($score > 0) {
            return SentimentScore::POSITIVE();
        } else if ($score < 0) {
            return SentimentScore::NEGATIVE();
        } else {
            if ($documentSentiment->getMagnitude() > 1) {
                return SentimentScore::MIXED();
            } else {
                return SentimentScore::NEUTRAL();
            }
        }

    }

    /**
     * GoogleSentimentAnalyzer destructor.
     */
    public function __destruct()
    {
        $this->languageServiceClient->close();
    }
}