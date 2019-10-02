<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\TwitterSearch;
use GuzzleHttp\Client;

class TwitterClient extends Client implements \App\Domain\Service\TwitterClient
{
    private $data;

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
        $this->data = [];
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

            $continue = true;
            do {
                $JSONResponse = json_decode($this->get('search/tweets.json', $options)->getBody()->getContents());
                $this->data = array_merge($this->data, $JSONResponse->statuses);
                if (property_exists($JSONResponse->search_metadata, 'next_results')) {
                    parse_str(parse_url($JSONResponse->search_metadata->next_results, PHP_URL_QUERY), $options['query']);
                } else {
                    $continue = false;
                }
            } while ($continue);
        } catch (\Exception $e) {
            return false;
        }

        return $this->data;
    }
}
