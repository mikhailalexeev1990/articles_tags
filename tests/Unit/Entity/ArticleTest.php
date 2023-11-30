<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ArticleTest extends UnitTestCase
{
    public function testIdIsNullOnNewEntity(): void
    {
        $article = new Article();
        $this->assertNull($article->getId());
    }

    public function testSetTitle(): void
    {
        $article = new Article();
        $article->setTitle('Test Article');
        $this->assertSame('Test Article', $article->getTitle());
    }

    public function testArticleTagsIsArrayCollection(): void
    {
        $article = new Article();
        $this->assertInstanceOf(ArrayCollection::class, $article->getArticleTags());
    }

    public function testArticleTagsIsEmptyOnNewEntity(): void
    {
        $article = new Article();
        $this->assertCount(0, $article->getArticleTags());
    }

    public function testAddArticleTag(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setTag($tag);

        $article->addArticleTag($articleTag);

        $this->assertCount(1, $article->getArticleTags());
        $this->assertSame($article, $articleTag->getArticle());
    }

    public function testRemoveArticleTag(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setTag($tag);

        $article->addArticleTag($articleTag);
        $article->removeArticleTag($articleTag);

        $this->assertCount(0, $article->getArticleTags());
    }
}
