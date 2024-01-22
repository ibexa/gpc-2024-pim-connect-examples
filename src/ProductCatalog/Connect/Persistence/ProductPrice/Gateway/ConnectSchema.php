<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice\Gateway;

final class ConnectSchema
{
    public const ID = 'id';
    public const PRODUCT_CODE = 'product_code';
    public const AMOUNT = 'amount';
    public const CURRENCY_ID = 'currency_id';
    public const DISCR = 'discriminator';
    public const CUSTOM_PRICE = 'custom_price_amount';
    public const CUSTOM_PRICE_RULE = 'custom_price_rule';
}
