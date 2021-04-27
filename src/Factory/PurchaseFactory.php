<?php

namespace App\Factory;

use App\Entity\Purchase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\ModelFactory;
use App\Repository\PurchaseRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method static Purchase|Proxy createOne(array $attributes = [])
 * @method static Purchase[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Purchase|Proxy findOrCreate(array $attributes)
 * @method static Purchase|Proxy random(array $attributes = [])
 * @method static Purchase|Proxy randomOrCreate(array $attributes = [])
 * @method static Purchase[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Purchase[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PurchaseRepository|RepositoryProxy repository()
 * @method Purchase|Proxy create($attributes = [])
 */
final class PurchaseFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'fullName' => self::faker()->name,
            'address' => self::faker()->streetAddress,
            'postalCode' => self::faker()->postcode,
            'city' => self::faker()->city,
            'purchasedAt' => self::faker()->dateTimeThisMonth(),
            'status' => self::faker()->boolean(90) ? Purchase::STATUS_PAID : Purchase::STATUS_PENDING
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            ->afterInstantiate(function (Purchase $purchase) {
                $total = 0;
                foreach ($purchase->getPurchaseItems() as $purchaseItem) {
                    $total += $purchaseItem->getTotal();
                }
                $purchase->setTotal($total);
            })
            ->afterInstantiate(function (Purchase $purchase) {
                $total = 0;
                foreach ($purchase->getPurchaseItems() as $purchaseItem) {
                    $total += $purchaseItem->getTotal();
                }
                $purchase->setTotal($total);
            });
    }

    protected static function getClass(): string
    {
        return Purchase::class;
    }
}
