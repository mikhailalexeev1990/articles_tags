<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Tag;
use App\Tests\Unit\UnitTestCase;

class TagTest extends UnitTestCase
{
    public function testGetId(): void
    {
        $tag = new Tag();

        $this->assertNull($tag->getId());
    }

    public function testGetName(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $this->assertSame('Test Tag', $tag->getName());
    }

    public function testSetName(): void
    {
        $tag = new Tag();
        $tag->setName('Test Tag');

        $this->assertSame('Test Tag', $tag->getName());
    }
}
