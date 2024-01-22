<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Contracts\CorePersistence\Gateway;

use App\Service\ConnectService;

abstract class AbstractConnect
{
    public function __construct(protected ConnectService $connectService)
    {
    }

    abstract protected function doInsert(array $data): int;

    abstract protected function doDelete(array $criteria): void;

    abstract protected function doUpdate(array $criteria, array $data): void;

    abstract public function findOneBy(array $criteria, ?array $orderBy = null): ?array;
}
