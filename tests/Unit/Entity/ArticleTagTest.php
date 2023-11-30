<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Article;
use App\Entity\ArticleTag;
use App\Entity\Tag;
use App\Tests\Unit\UnitTestCase;

class ArticleTagTest extends UnitTestCase
{
    public function testAssignCreatedAtOnPrePersist(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setArticle($article);
        $articleTag->setTag($tag);
        $articleTag->assignCreatedAt();

        $this->assertInstanceOf(\DateTimeInterface::class, $articleTag->getCreatedAt());
    }

    public function testGetArticle(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setArticle($article);

        $this->assertSame($article, $articleTag->getArticle());
    }

    public function testSetArticle(): void
    {
        $article = new Article();
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setArticle($article);

        $this->assertSame($article, $articleTag->getArticle());
    }

    public function testGetTag(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setTag($tag);

        $this->assertSame($tag, $articleTag->getTag());
    }

    public function testSetTag(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $articleTag = new ArticleTag();
        $articleTag->setTag($tag);

        $this->assertSame($tag, $articleTag->getTag());
    }
}
