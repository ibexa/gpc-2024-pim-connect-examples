<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\Controller\Checkout\Step;

use App\Form\Type\ProductAvailabilityType;
use App\Service\ConnectService;
use Ibexa\Bundle\Checkout\Controller\AbstractStepController;
use Ibexa\Cart\Factory\CartSummaryFactory;
use Ibexa\Checkout\Workflow\CheckoutWorkflow;
use Ibexa\Contracts\Cart\CartServiceInterface;
use Ibexa\Contracts\Checkout\CheckoutServiceInterface;
use Ibexa\Contracts\Checkout\Value\CheckoutInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProductAvailabilityController extends AbstractStepController
{
    public function __construct(
        private CheckoutServiceInterface $checkoutService,
        protected CheckoutWorkflow $checkoutWorkflow,
        protected CartServiceInterface $cartService,
        protected CartSummaryFactory $cartSummaryFactory,
        public ConnectService $connectService
    ) {
        parent::__construct(
            $checkoutService,
            $checkoutWorkflow,
            $cartService,
            $cartSummaryFactory
        );
    }

    public function __invoke(
        Request $request,
        CheckoutInterface $checkout,
        string $step
    ): Response {
        $form = $this->createStepForm($step, ProductAvailabilityType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->advance($checkout, $step, $form->getData());
        }

        return $this->render(
            '@ibexadesign/checkout/step/product_availability.html.twig',
            [
                'entries' => $this->getProductAvailability($checkout->getCartIdentifier()),
                'current_step' => $step,
                'checkout' => $checkout,
                'form' => $form->createView(),
            ]
        );
    }

    private function getProductAvailability(string $cartIdentifier): array
    {
        $availability = [];
        $cart = $this->getCart($cartIdentifier);
        foreach ($cart->getEntries() as $entry) {
            /** @var \Ibexa\Contracts\Cart\Value\EntryInterface $entry */
            $availability[] = [
                'entry' => $entry,
                'availability' => $this->connectService->checkProductAvailabilityByCode($entry->getProduct()->getCode(), $entry->getQuantity()),
            ];
        }

        return $availability;
    }
}
