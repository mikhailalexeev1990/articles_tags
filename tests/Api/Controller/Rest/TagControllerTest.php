<?php

namespace App\Tests\Api\Controller\Rest;

use App\Entity\Tag;
use App\Tests\Factory\TagFactory;
use App\Tests\Traits\TruncateTableAwareTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TagControllerTest extends WebTestCase
{
    use TruncateTableAwareTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->truncateEntityTables([
            Tag::class,
        ]);
    }

    public function testCreateTag(): void
    {
        $data = ['name' => 'qwerwqer 1'];

        $this->client->jsonRequest(Request::METHOD_POST, '/api/v1/tags', $data);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testUpdateTag(): void
    {
        $tag = TagFactory::createOne(['name' => 'Tag 1']);
        $tagId = $tag->getId();
        $data = ['name' => 'Tag test'];

        $this->client->jsonRequest(Request::METHOD_PATCH, "/api/v1/tags/$tagId", $data);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Tag test', $responseData['name']);
    }
}
