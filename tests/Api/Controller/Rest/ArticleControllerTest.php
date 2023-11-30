<?php

namespace App\Tests\Api\Controller\Rest;

use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\ArticleTagFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Traits\TruncateTableAwareTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use function Zenstruck\Foundry\faker;

class ArticleControllerTest extends WebTestCase
{
    use TruncateTableAwareTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->truncateEntityTables([
            ArticleTag::class,
            Article::class,
            Tag::class,
        ]);
    }

    public function testList(): void
    {
        $tag1 = TagFactory::createOne(['name' => 'Tag 1']);
        $tag2 = TagFactory::createOne(['name' => 'Tag 2']);
        $tag3 = TagFactory::createOne(['name' => 'Tag 3']);
        $article1 = ArticleFactory::createOne(['title' => 'Article 1']);
        $article2 = ArticleFactory::createOne(['title' => 'Article 2']);
        $article3 = ArticleFactory::createOne(['title' => 'Article 3']);
        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag1]);
        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag2]);
        ArticleTagFactory::createOne(['article' => $article1, 'tag' => $tag3]);
        ArticleTagFactory::createOne(['article' => $article2, 'tag' => $tag1]);
        ArticleTagFactory::createOne(['article' => $article2, 'tag' => $tag2]);
        ArticleTagFactory::createOne(['article' => $article3, 'tag' => $tag2]);
        $tagIds = [$tag1->getId(), $tag2->getId(), $tag3->getId()];
        $queryParams = http_build_query(['tags' => $tagIds]);

        $this->client->jsonRequest(Request::METHOD_GET, "/api/v1/articles?$queryParams");
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $responseData);
        $this->assertEquals('Article 1', $responseData[0]['title']);
    }

    public function testGetArticle(): void
    {
        $articleId = ArticleFactory::createOne()->getId();

        $this->client->jsonRequest(Request::METHOD_GET, "/api/v1/articles/$articleId");
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testCreateArticle(): void
    {
        $data = [
            'title' => faker()->title(),
            'articleTags' => [
                ['tag' => TagFactory::createOne(['name' => 'Tag 1'])->getId()],
                ['tag' => TagFactory::createOne(['name' => 'Tag 2'])->getId()],
                ['tag' => TagFactory::createOne(['name' => 'Tag 3'])->getId()],
            ]
        ];

        $this->client->jsonRequest(Request::METHOD_POST, '/api/v1/articles', $data);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('id', $responseData);
    }

    public function testUpdateArticle(): void
    {
        $article = ArticleFactory::createOne(['title' => 'Article 1']);
        $tag1 = TagFactory::createOne(['name' => 'Tag 1']);
        $tag2 = TagFactory::createOne(['name' => 'Tag 2']);
        $tag3 = TagFactory::createOne(['name' => 'Tag 3']);
        ArticleTagFactory::createOne(['article' => $article, 'tag' => $tag1]);
        ArticleTagFactory::createOne(['article' => $article, 'tag' => $tag2]);
        $articleId = $article->getId();
        $data = [
            'title' => 'Article test',
            'articleTags' => [
                ['tag' => $tag2->getId()],
                ['tag' => $tag3->getId()],
            ]
        ];

        $this->client->jsonRequest(Request::METHOD_PATCH, "/api/v1/articles/$articleId", $data);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertNotEmpty($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Article test', $responseData['title']);
    }

    public function testDeleteArticle(): void
    {
        $articleId = ArticleFactory::createOne()->getId();

        $this->client->jsonRequest(Request::METHOD_DELETE, "/api/v1/articles/$articleId");

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }
}
