<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\TwitterSearch;
use GuzzleHttp\Client;

class TwitterClient extends Client implements \App\Domain\Service\TwitterClient
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * Find tweets by text.
     *
     * @param TwitterSearch $twitterSearch
     * @return bool|mixed
     */
    public function findTweetsWith(
        TwitterSearch $twitterSearch
    ) {
        try {

            $options = [
                'auth' => 'oauth',
                'query' => [
                    'q' => $twitterSearch->hashtag()->getName(),
                    'include_entities' => $twitterSearch->includeEntities(),
                    'result_type' => $twitterSearch->resultType(),
                    'count' => $twitterSearch->count(),
                    'since_id' => $twitterSearch->hashtag()->getLastTweet()
                ]
            ];

            $JSONResponse = $this->get('search/tweets.json', $options)->getBody()->getContents();

            return json_decode($JSONResponse);
        } catch (\Exception $e) {
            return false;
        }
    }
}
