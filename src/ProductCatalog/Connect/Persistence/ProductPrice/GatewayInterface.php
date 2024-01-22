<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice;

use App\ProductCatalog\Contracts\CorePersistence\Gateway\GatewayInterface as BaseGatewayInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;

interface GatewayInterface extends BaseGatewayInterface
{
    public function insert(ProductPriceCreateStructInterface $createStruct): int;

    public function update(ProductPriceUpdateStructInterface $updateStruct): void;

    public function delete(int $id): void;

    public function countBy($criteria): int;

    public function findById(int $id): ?array;

    public function findBy($criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array;

    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria
    ): ?array;

    public function updateProductCode(string $newProductCode, string $oldProductCode): void;
}
