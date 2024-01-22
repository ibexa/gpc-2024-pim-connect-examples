<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductAvailability\Gateway;

use App\ProductCatalog\Connect\Persistence\ProductAvailability\Contracts\AbstractAvailabilityGateway;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\GatewayInterface;

final class Connect extends AbstractAvailabilityGateway implements GatewayInterface
{
    public function insert(ProductAvailabilityCreateStruct $createStruct): int
    {
        return $this->doInsert([
            ConnectSchema::PRODUCT_CODE => $createStruct->getProduct()->getCode(),
            ConnectSchema::AVAILABILITY => $createStruct->getAvailability(),
            ConnectSchema::STOCK => $createStruct->getStock(),
            ConnectSchema::IS_INFINITE => $createStruct->isInfinite(),
        ]);
    }

    public function update(ProductAvailabilityUpdateStruct $updateStruct): void
    {
        $data = [];
        if ($updateStruct->getAvailability() !== null) {
            $data[ConnectSchema::AVAILABILITY] = $updateStruct->getAvailability();
        }
        if ($updateStruct->isInfinite() !== null) {
            $data[ConnectSchema::IS_INFINITE] = $updateStruct->isInfinite();
        }
        if ($updateStruct->getStock() !== null || $updateStruct->isInfinite() === true) {
            $data[ConnectSchema::STOCK] = $updateStruct->getStock();
        }

        $criteria = [
            ConnectSchema::PRODUCT_CODE => $updateStruct->getProduct()->getCode(),
        ];
        if (!empty($data)) {
            $this->doUpdate($criteria, $data);
        }
    }

    public function find(string $productCode): ?array
    {
        return $this->findOneBy([
            ConnectSchema::PRODUCT_CODE => $productCode,
        ]);
    }

    public function findAggregatedForBaseProduct(string $productCode, int $productSpecificationId): ?array
    {
        // @TODO
        return null;
    }

    public function deleteByProductCode(string $productCode): void
    {
        $this->doDelete([
            ConnectSchema::PRODUCT_CODE => $productCode,
        ]);
    }

    public function deleteByBaseProductCodeWithVariants(
        string $baseProductCode,
        int $productSpecificationId
    ): void {
        // @TODO
    }

    public function updateProductCode(string $newProductCode, string $oldProductCode): void
    {
        $data[ConnectSchema::PRODUCT_CODE] = $newProductCode;

        $criteria = [
            ConnectSchema::PRODUCT_CODE => $oldProductCode,
        ];

        $this->doUpdate($criteria, $data);
    }
}
