<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace App\ProductCatalog\Connect\Persistence\ProductPrice\Contracts;

use App\ProductCatalog\Contracts\CorePersistence\Gateway\AbstractConnect;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractPriceGateway extends AbstractConnect
{
    protected function doInsert(array $data): int
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'data' => $data,
        ];

        $response = $this->connectService->handleRequest(Request::METHOD_POST, $payload, $headers);

        return 0;
    }

    protected function doDelete(array $criteria): void
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'criteria' => $criteria,
        ];

        $this->connectService->handleRequest(Request::METHOD_DELETE, $payload, $headers);
    }

    protected function doUpdate(array $criteria, array $data): void
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
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
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'result' => 'single',
            'criteria' => $criteria,
            'order_by' => $orderBy,
        ];

        $result = $this->connectService->handleRequest(Request::METHOD_GET, $payload, $headers);

        return empty($result) ? null : $result;
    }

    public function countBy($criteria): int
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'function' => 'count',
            'criteria' => $criteria,
        ];

        $response = $this->connectService->handleRequest(Request::METHOD_GET, $payload, $headers);

        return 0; // @TODO
    }

    public function countAll(): int
    {
        return $this->countBy([]);
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        return $this->findBy([], null, $limit, $offset);
    }

    public function findBy($criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'result' => 'multiple',
            'order_by' => $orderBy,
            'limit' => $limit,
            'offset' => $offset,
            'criteria' => $criteria,
        ];

        return $this->connectService->handleRequest(Request::METHOD_GET, $payload, $headers);
    }

    public function findById(int $id): ?array
    {
        $headers = [
            'Accept' => 'application/api.ProductPrice+json',
        ];

        $payload = [
            'result' => 'single',
            'criteria' => ['id' => $id],
        ];

        $result = $this->connectService->handleRequest(Request::METHOD_GET, $payload, $headers);

        return empty($result) ? null : $result;
    }
}
