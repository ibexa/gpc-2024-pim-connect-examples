<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Mapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function create(ProductPriceCreateStructInterface $createStruct): int
    {
        return $this->gateway->insert($createStruct);
    }

    public function update(ProductPriceUpdateStructInterface $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function delete(int $id): void
    {
        $this->gateway->delete($id);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function find(int $id): AbstractProductPrice
    {
        $row = $this->gateway->findById($id);

        if ($row === null) {
            throw new NotFoundException(AbstractProductPrice::class, $id);
        }

        return $this->mapper->createFromRow($row);
    }

    public function exists(int $id): bool
    {
        $row = $this->gateway->findById($id);

        return $row !== null;
    }

    public function countBy(array $criteria = []): int
    {
        return $this->gateway->countBy($criteria);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $rows = $this->gateway->findBy($criteria, $orderBy, $limit, $offset);

        return $this->mapper->createFromRows($rows);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?AbstractProductPrice
    {
        $row = $this->gateway->findOneBy($criteria, $orderBy);

        if ($row === null) {
            return null;
        }

        return $this->mapper->createFromRow($row);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria = []
    ): ?AbstractProductPrice {
        $row = $this->gateway->findOneByProductCode($productCode, $currencyId, $discriminator, $criteria);

        if ($row === null) {
            return null;
        }

        return $this->mapper->createFromRow($row);
    }

    public function updateProductCode(string $newProductCode, string $oldProductCode): void
    {
        $this->gateway->updateProductCode($newProductCode, $oldProductCode);
    }
}
