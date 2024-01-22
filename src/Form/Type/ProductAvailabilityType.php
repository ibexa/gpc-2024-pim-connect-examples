<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductAvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => /** @Desc("Go to billing & shipping address") */ 'ibexa_checkout.ui.go_to_billing_and_shipping_address.label',
                    'attr' => ['class' => 'btn btn-black icon-arrow-right-white mr-40 mb-20'],
                ]
            );
    }
}
