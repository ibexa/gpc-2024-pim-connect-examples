<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace App\Subscriber;

use Ibexa\Contracts\OrderManagement\Event\CreateOrderEvent;
use Ibexa\Prototype\Connect\Service\IbexaConnectService;
use Ibexa\Prototype\Connect\Service\SettingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateOrderSubscriber implements EventSubscriberInterface
{
    private IbexaConnectService $connectService;

    private SettingService $settingService;

    public function __construct(IbexaConnectService $connectService, SettingService $settingService)
    {
        $this->connectService = $connectService;
        $this->settingService = $settingService;
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateOrderEvent::class => ['onCreateOrder'],
        ];
    }

    public function onCreateOrder(CreateOrderEvent $event)
    {
        if ($this->settingService->isEnabled()) {
            $order = $event->getOrderResult();

            $payload = [];
            $payload['order']['id'] = $order->getId();
            $payload['order']['identifier'] = $order->getIdentifier();
            $payload['order']['status'] = $order->getStatus();

            $this->connectService->sendData(null, 'ibexa_order', $payload);
        }
    }
}
