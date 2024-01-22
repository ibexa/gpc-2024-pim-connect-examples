<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\ProductCatalog\Connect\Persistence\ProductPrice\Gateway;

use App\ProductCatalog\Connect\Persistence\ProductPrice\Contracts\AbstractPriceGateway;
use App\ProductCatalog\Connect\Persistence\ProductPrice\GatewayInterface;
use App\Service\ConnectService;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;

final class Connect extends AbstractPriceGateway implements GatewayInterface
{
    private DecimalMoneyFactory $decimalMoneyFactory;

    public function __construct(
        ConnectService $connectService,
        DecimalMoneyFactory $decimalMoneyFactory,
    ) {
        parent::__construct($connectService);
        $this->decimalMoneyFactory = $decimalMoneyFactory;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?array
    {
        $orderBy ??= [ConnectSchema::ID => 'DESC'];

        return parent::findOneBy($criteria, $orderBy);
    }

    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria
    ): ?array {
        $criteria = $criteria + [
            ConnectSchema::PRODUCT_CODE => $productCode,
            ConnectSchema::CURRENCY_ID => $currencyId,
            ConnectSchema::DISCR => $discriminator,
        ];

        return $this->findOneBy($criteria);
    }

    public function insert(ProductPriceCreateStructInterface $createStruct): int
    {
        $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
        $customPrice = $createStruct->getCustomPrice();
        $customPriceRule = $createStruct->getCustomPriceRule();

        $id = $this->doInsert([
            ConnectSchema::PRODUCT_CODE => $createStruct->getProduct()->getCode(),
            ConnectSchema::AMOUNT => $formatter->format($createStruct->getMoney()),
            ConnectSchema::CURRENCY_ID => $createStruct->getCurrency()->getId(),
            ConnectSchema::DISCR => $createStruct::getDiscriminator(),
            ConnectSchema::CUSTOM_PRICE => $customPrice === null ? null : $formatter->format($customPrice),
            ConnectSchema::CUSTOM_PRICE_RULE => $customPriceRule,
        ]);

        return $id;
    }

    public function update(ProductPriceUpdateStructInterface $updateStruct): void
    {
        $data = [];
        if ($updateStruct->getMoney() !== null) {
            $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
            $data[ConnectSchema::AMOUNT] = $formatter->format($updateStruct->getMoney());
        }

        $customPriceMoney = $updateStruct->getCustomPriceMoney();
        if ($customPriceMoney !== null) {
            $formatter = $this->decimalMoneyFactory->getMoneyFormatter();
            $customPriceMoney = $formatter->format($customPriceMoney);
        }

        $data[ConnectSchema::CUSTOM_PRICE] = $customPriceMoney;
        $data[ConnectSchema::CUSTOM_PRICE_RULE] = $updateStruct->getCustomPriceRule();

        $criteria = [
            ConnectSchema::ID => $updateStruct->getPrice()->getId(),
        ];

        $this->doUpdate($criteria, $data);
    }

    public function delete(int $id): void
    {
        $this->doDelete([
            ConnectSchema::ID => $id,
        ]);
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
