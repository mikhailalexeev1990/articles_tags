<?php

namespace App\Tests\Factory;

use App\Entity\ArticleTag;
use App\Repository\ArticleTagRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ArticleTag>
 *
 * @method        ArticleTag|Proxy                     create(array|callable $attributes = [])
 * @method static ArticleTag|Proxy                     createOne(array $attributes = [])
 * @method static ArticleTag|Proxy                     find(object|array|mixed $criteria)
 * @method static ArticleTag|Proxy                     findOrCreate(array $attributes)
 * @method static ArticleTag|Proxy                     first(string $sortedField = 'id')
 * @method static ArticleTag|Proxy                     last(string $sortedField = 'id')
 * @method static ArticleTag|Proxy                     random(array $attributes = [])
 * @method static ArticleTag|Proxy                     randomOrCreate(array $attributes = [])
 * @method static ArticleTagRepository|RepositoryProxy repository()
 * @method static ArticleTag[]|Proxy[]                 all()
 * @method static ArticleTag[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static ArticleTag[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static ArticleTag[]|Proxy[]                 findBy(array $attributes)
 * @method static ArticleTag[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static ArticleTag[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class ArticleTagFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ArticleTag $articleTag): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ArticleTag::class;
    }
}
