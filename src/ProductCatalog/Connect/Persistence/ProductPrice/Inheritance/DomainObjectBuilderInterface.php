<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice\Inheritance;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;

interface DomainObjectBuilderInterface
{
    public function buildDomainPriceObject(
        AbstractProductPrice $spiPrice,
        ?ProductInterface $product = null
    ): PriceInterface;
}
