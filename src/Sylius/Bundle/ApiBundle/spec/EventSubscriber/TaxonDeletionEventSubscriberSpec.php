<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class TaxonDeletionEventSubscriberSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus, ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($eventBus, $channelRepository);
    }

    function it_allows_to_remove_taxon_if_any_channel_has_not_it_as_a_menu_taxon(
        MessageBusInterface $eventBus,
        TaxonInterface $taxon,
        HttpKernelInterface $kernel,
        Request $request,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);
        $taxon->getCode()->willReturn('WATCHES');
        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn(null);

        $this->protectFromRemovingMenuTaxon(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $taxon->getWrappedObject(),
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        MessageBusInterface $eventBus,
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);
        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->protectFromRemovingMenuTaxon(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass(),
        ));
    }

    function it_throws_an_exception_if_a_subject_is_menu_taxon(
        MessageBusInterface $eventBus,
        TaxonInterface $taxon,
        HttpKernelInterface $kernel,
        Request $request,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);
        $taxon->getCode()->willReturn('WATCHES');
        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn($channel);

        $this
            ->shouldThrow(\Exception::class)
            ->during('protectFromRemovingMenuTaxon', [new ViewEvent(
                $kernel->getWrappedObject(),
                $request->getWrappedObject(),
                HttpKernelInterface::MASTER_REQUEST,
                $taxon->getWrappedObject(),
            )])
        ;
    }
}
