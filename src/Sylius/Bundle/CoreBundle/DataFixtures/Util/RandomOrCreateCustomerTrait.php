<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Util;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\RandomOrCreateResourceEvent;
use Sylius\Bundle\CoreBundle\DataFixtures\Event\ResourceEventInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Zenstruck\Foundry\Proxy;

trait RandomOrCreateCustomerTrait
{
    /**
     * @return CustomerInterface|Proxy
     */
    private function randomOrCreateCustomer(EventDispatcherInterface $eventDispatcher): Proxy
    {
        /** @var ResourceEventInterface $event */
        $event = $eventDispatcher->dispatch(
            new RandomOrCreateResourceEvent(CustomerFactoryInterface::class)
        );

        return $event->getResource();
    }
}