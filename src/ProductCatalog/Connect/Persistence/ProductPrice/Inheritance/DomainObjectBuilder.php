<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice\Inheritance;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Exception\MissingHandlingServiceException;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\DomainMapperInterface as CurrencyDomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Inheritance\DomainMapperInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Repository\ProxyDomainMapper;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency as MoneyCurrency;

class DomainObjectBuilder implements DomainObjectBuilderInterface
{
    private DecimalMoneyFactory $decimalMoneyParserFactory;

    private ProxyDomainMapper $proxyDomainMapper;

    private CurrencyDomainMapperInterface $currencyDomainMapper;

    public function __construct(
        DecimalMoneyFactory $decimalMoneyParserFactory,
        ProxyDomainMapper $proxyDomainMapper,
        CurrencyDomainMapperInterface $currencyInstantiator,
        iterable $domainMappers
    ) {
        $this->decimalMoneyParserFactory = $decimalMoneyParserFactory;
        $this->domainMappers = $domainMappers;
        $this->proxyDomainMapper = $proxyDomainMapper;
        $this->currencyDomainMapper = $currencyInstantiator;
    }

    public function buildDomainPriceObject(
        AbstractProductPrice $spiPrice,
        ?ProductInterface $product = null
    ): PriceInterface {
        $moneyParser = $this->decimalMoneyParserFactory->getMoneyParser();
        $moneyFormatter = $this->decimalMoneyParserFactory->getMoneyFormatter();
        $moneyCurrency = new MoneyCurrency($spiPrice->getCurrency()->code);
        $money = $moneyParser->parse($spiPrice->getAmount(), $moneyCurrency);
        $product = $product ?? $this->proxyDomainMapper->createProductProxy($spiPrice->getProductCode());
        $currency = $this->currencyDomainMapper->createFromSpi($spiPrice->getCurrency());

        foreach ($this->domainMappers as $spiMapper) {
            if ($spiMapper->canMapSpiPrice($spiPrice)) {
                return $spiMapper->mapSpiPrice(
                    $moneyFormatter,
                    $moneyParser,
                    $product,
                    $currency,
                    $spiPrice,
                    $money,
                );
            }
        }

        throw new MissingHandlingServiceException(
            $this->domainMappers,
            $spiPrice,
            DomainMapperInterface::class,
            'ibexa.product_catalog.product_price.inheritance.domain_mapper',
        );
    }
}
