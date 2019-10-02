<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 12/06/2018
 * Time: 17:20
 */

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HashtagControllerTest extends WebTestCase
{
    private $client;
    private $crawler;
    private $domain = 'http://localhost';

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', '/');
    }

    public function testHashtagsResponse()
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPageIsCompletelyRendered()
    {
        $this->assertEquals(1, $this->crawler->filter('footer')->count());
    }

    public function testHashtagsTableExists()
    {
        $this->assertGreaterThan(0,
            $this->crawler->filter('table#hashtagsTable tbody tr')
                ->count());
    }

    public function testHashtagLinkIfExists()
    {
        $randomTweetLink = $this->crawler
            ->filter('table#hashtagsTable tbody tr')
            ->selectLink('Random Tweet');

        if ($randomTweetLink->count() === 0) {
            $this->testHashtagsTableExists();
        } else {
            $index = rand(0, $randomTweetLink->count() - 1);
            $url = $this->domain . $randomTweetLink->eq($index)->attr('href');
            $this->client->click($randomTweetLink->eq($index)->link());
            $this->assertEquals($url, $this->client->getHistory()->current()->getUri());
            $this->testHashtagsResponse();
        }
    }

}