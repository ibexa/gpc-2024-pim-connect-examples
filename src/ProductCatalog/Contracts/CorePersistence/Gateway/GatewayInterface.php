<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Contracts\CorePersistence\Gateway;

interface GatewayInterface
{
    public function countAll(): int;

    public function countBy($criteria): int;

    public function findAll(?int $limit = null, int $offset = 0): array;

    public function findBy($criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array;

    public function findOneBy(array $criteria, ?array $orderBy = null): ?array;

    public function findById(int $id): ?array;
}
