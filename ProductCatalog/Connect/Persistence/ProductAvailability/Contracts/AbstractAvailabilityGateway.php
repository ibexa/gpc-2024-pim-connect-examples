<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace App\ProductCatalog\Connect\Persistence\ProductAvailability\Contracts;

use App\ProductCatalog\Contracts\CorePersistence\Gateway\AbstractConnect;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAvailabilityGateway extends AbstractConnect
{
    protected function doInsert(array $data): int
    {
        $headers = [
            'Accept' => 'application/api.ProductAvailability+json',
        ];

        $payload = [
            'data' => $data,
        ];

        $response = $this->connectService->handleRequest(Request::METHOD_POST, $payload, $headers);

        return 0; //@TODO
    }

    protected function doDelete(array $criteria): void
    {
        $headers = [
            'Accept' => 'application/api.ProductAvailability+json',
        ];

        $payload = [
            'criteria' => $criteria,
        ];

        $this->connectService->handleRequest(Request::METHOD_DELETE, $payload, $headers);
    }

    protected function doUpdate(array $criteria, array $data): void
    {
        $headers = [
            'Accept' => 'application/api.ProductAvailability+json',
        ];

        $payload = [
            'criteria' => $criteria,
            'data' => $data,
        ];

        $this->connectService->handleRequest(Request::METHOD_PATCH, $payload, $headers);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?array
    {
        $headers = [
            'Accept' => 'application/api.ProductAvailability+json',
        ];

        $payload = [
            'criteria' => $criteria,
            'order_by' => $orderBy,
        ];

        return $this->connectService->handleRequest(Request::METHOD_GET, $payload, $headers);
    }
}
