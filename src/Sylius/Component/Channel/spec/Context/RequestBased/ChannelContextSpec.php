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

namespace spec\Sylius\Component\Channel\Context\RequestBased;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ChannelContextSpec extends ObjectBehavior
{
    function let(RequestResolverInterface $requestResolver, RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestResolver, $requestStack);
    }

    function it_implements_channel_context_interface(): void
    {
        $this->shouldImplement(ChannelContextInterface::class);
    }

    function it_proxies_master_request_to_request_resolver(
        RequestResolverInterface $requestResolver,
        RequestStack $requestStack,
        Request $masterRequest,
        ChannelInterface $channel,
    ): void {
        $requestStack->getMainRequest()->willReturn($masterRequest);

        $requestResolver->findChannel($masterRequest)->willReturn($channel);

        $this->getChannel()->shouldReturn($channel);
    }

    function it_throws_a_channel_not_found_exception_if_request_resolver_returns_null(
        RequestResolverInterface $requestResolver,
        RequestStack $requestStack,
        Request $masterRequest,
    ): void {
        $requestStack->getMainRequest()->willReturn($masterRequest);

        $requestResolver->findChannel($masterRequest)->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }

    function it_throws_a_channel_not_found_exception_if_there_is_no_master_request(
        RequestStack $requestStack,
    ): void {
        $requestStack->getMainRequest()->willReturn(null);

        $this->shouldThrow(ChannelNotFoundException::class)->during('getChannel');
    }
}
