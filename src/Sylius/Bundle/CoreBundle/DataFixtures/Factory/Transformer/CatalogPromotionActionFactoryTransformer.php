<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;

final class CatalogPromotionActionFactoryTransformer implements CatalogPromotionActionFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        if ($attributes['type'] !== FixedDiscountPriceCalculator::TYPE) {
            return $attributes;
        }

        $configuration = &$attributes['configuration'];

        foreach ($configuration as $channelCode => $channelConfiguration) {
            if (isset($channelConfiguration['amount'])) {
                $configuration[$channelCode]['amount'] = (int) ($channelConfiguration['amount'] * 100);
            }
        }

        return $attributes;
    }
}