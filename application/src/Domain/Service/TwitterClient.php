<?php

namespace App\Domain\Service;

interface TwitterClient
{
    public function findTweetsWith(TwitterSearch $twitterSearch);
}