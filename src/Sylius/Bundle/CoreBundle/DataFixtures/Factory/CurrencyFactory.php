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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues\CurrencyDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\State\WithCodeTrait;
use Sylius\Bundle\CoreBundle\DataFixtures\Transformer\CurrencyTransformerInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Updater\CurrencyUpdaterInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CurrencyInterface>
 *
 * @method static CurrencyInterface|Proxy createOne(array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static CurrencyInterface|Proxy find(object|array|mixed $criteria)
 * @method static CurrencyInterface|Proxy findOrCreate(array $attributes)
 * @method static CurrencyInterface|Proxy first(string $sortedField = 'id')
 * @method static CurrencyInterface|Proxy last(string $sortedField = 'id')
 * @method static CurrencyInterface|Proxy random(array $attributes = [])
 * @method static CurrencyInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] all()
 * @method static CurrencyInterface[]|Proxy[] findBy(array $attributes)
 * @method static CurrencyInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CurrencyInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CurrencyInterface|Proxy create(array|callable $attributes = [])
 */
class CurrencyFactory extends ModelFactory implements CurrencyFactoryInterface, FactoryWithModelClassAwareInterface
{
    use WithCodeTrait;

    private static ?string $modelClass = null;

    public function __construct(
        private FactoryInterface $currencyFactory,
        private CurrencyDefaultValuesInterface $defaultValues,
        private CurrencyTransformerInterface $transformer,
        private CurrencyUpdaterInterface $updater,
    ) {
        parent::__construct();
    }

    public static function withModelClass(string $modelClass): void
    {
        self::$modelClass = $modelClass;
    }

    protected function getDefaults(): array
    {
        return $this->defaultValues->getDefaults(self::faker());
    }

    protected function transform(array $attributes): array
    {
        return $this->transformer->transform($attributes);
    }

    protected function update(CurrencyInterface $currency, array $attributes): void
    {
        $this->updater->update($currency, $attributes);
    }

    protected function initialize(): self
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                return $this->transform($attributes);
            })
            ->instantiateWith(function(array $attributes): CurrencyInterface {
                /** @var CurrencyInterface $currency */
                $currency = $this->currencyFactory->createNew();

                $this->update($currency, $attributes);

                return $currency;
            })
        ;
    }

    protected static function getClass(): string
    {
        return self::$modelClass ?? Currency::class;
    }
}
