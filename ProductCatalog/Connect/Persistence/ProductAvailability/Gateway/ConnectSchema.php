<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductAvailability\Gateway;

final class ConnectSchema
{
    public const ID = 'id';
    public const PRODUCT_CODE = 'product_code';
    public const AVAILABILITY = 'availability';
    public const STOCK = 'stock';
    public const IS_INFINITE = 'is_infinite';
}
