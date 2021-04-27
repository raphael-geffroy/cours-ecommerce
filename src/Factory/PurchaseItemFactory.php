<?php

namespace App\Factory;

use App\Entity\PurchaseItem;
use App\Repository\PurchaseItemRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static PurchaseItem|Proxy createOne(array $attributes = [])
 * @method static PurchaseItem[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static PurchaseItem|Proxy findOrCreate(array $attributes)
 * @method static PurchaseItem|Proxy random(array $attributes = [])
 * @method static PurchaseItem|Proxy randomOrCreate(array $attributes = [])
 * @method static PurchaseItem[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PurchaseItem[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PurchaseItemRepository|RepositoryProxy repository()
 * @method PurchaseItem|Proxy create($attributes = [])
 */
final class PurchaseItemFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
        self::faker()->addProvider(new \Bezhanov\Faker\Provider\Commerce(self::faker()));
        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'quantity' => self::faker()->numberBetween(1, 3),
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            ->afterInstantiate(function (PurchaseItem $purchaseItem) {
                $purchaseItem->setProductName($purchaseItem->getProduct()->getName())
                    ->setProductPrice($purchaseItem->getProduct()->getPrice())
                    ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity());
            });
    }

    protected static function getClass(): string
    {
        return PurchaseItem::class;
    }
}
