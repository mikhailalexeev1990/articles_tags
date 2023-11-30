<?php

namespace App\Tests\Traits;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait TruncateTableAwareTrait
{
    abstract protected static function getContainer(): ContainerInterface;

    protected function truncateEntityTables(array $entityClasses = []): void
    {
        /** @var ManagerRegistry $mr */
        $mr = static::getContainer()->get(ManagerRegistry::class);
        foreach ($entityClasses as $entityClass) {
            $manager = $mr->getManagerForClass($entityClass);
            if ($manager === null) {
                continue;
            }

            $manager->getConnection()->executeStatement(
                "TRUNCATE TABLE {$manager->getClassMetadata($entityClass)->getTableName()} CASCADE"
            );
        }
    }
}
