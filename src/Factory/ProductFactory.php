<?php

namespace App\Factory;

use App\Entity\Product;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\ModelFactory;
use App\Repository\ProductRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @method static Product|Proxy createOne(array $attributes = [])
 * @method static Product[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Product|Proxy findOrCreate(array $attributes)
 * @method static Product|Proxy random(array $attributes = [])
 * @method static Product|Proxy randomOrCreate(array $attributes = [])
 * @method static Product[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Product[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ProductRepository|RepositoryProxy repository()
 * @method Product|Proxy create($attributes = [])
 */
final class ProductFactory extends ModelFactory
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        parent::__construct();
        $this->slugger = $slugger;
        self::faker()->addProvider(new \Liior\Faker\Prices(self::faker()));
        self::faker()->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider(self::faker()));
        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $name = self::faker()->productName();
        return [
            'name' => $name,
            'slug' => strtolower($this->slugger->slug($name)),
            'price' => self::faker()->price(4000, 20000),
            'shortDescription' => self::faker()->paragraph(),
            'mainPicture' => self::faker()->imageUrl(400, 400, true)
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Product $product) {})
        ;
    }

    protected static function getClass(): string
    {
        return Product::class;
    }
}
