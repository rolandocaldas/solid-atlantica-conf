<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 12:43
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RandomTweetControllerTest extends WebTestCase
{

    private $client;
    private $crawler;
    private $hashtagUrl = null;
    private $tweetPattern = '#^https:\/\/twitter\.com\/[^\/]+\/status\/[\d]+$#si';

    public function setUp()
    {
        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', $this->obtainHashtagUrl());
    }

    private function obtainHashtagUrl() : string
    {
        if ($this->hashtagUrl !== null) {
            return $this->hashtagUrl;
        }

        $this->hashtagUrl = $this->crawlHomepageToExtractRandomTweetUrl();

        return $this->hashtagUrl;
    }

    private function crawlHomepageToExtractRandomTweetUrl(int $index = 0) : string
    {
        return $this->client->request('GET', '/')
            ->filter('table#hashtagsTable tbody tr')
            ->selectLink('Random Tweet')->eq($index)
            ->attr('href');
    }

    public function testRandomTweetResponse()
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPageIsCompletelyRendered()
    {
        $this->assertEquals(1, $this->crawler->filter('footer')->count());
    }

    public function testRandomTweetIsShowed()
    {
        $this->assertRegExp($this->tweetPattern, $this->extractTweetUrl());
    }

    private function extractTweetUrl()
    {
        return $this->crawler->filter('.twitter-tweet a')->attr('href');
    }

    public function testRandomTweetIsReallyRandom()
    {
        $urls = $this->reloadRandomTweetAndExtractTweetUrl(10);

        $this->assertGreaterThan(1, count(array_unique($urls)));
    }

    private function reloadRandomTweetAndExtractTweetUrl(int $reloads = 5) : array
    {
        $urls = [];
        for ($i = 0; $i < $reloads; $i++) {
            $this->crawler = $this->client->reload();
            $urls[] = $this->extractTweetUrl();
        }
        return $urls;
    }
}