<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use App\Factory\UserFactory;
use App\Factory\ProductFactory;
use App\Factory\CategoryFactory;
use App\Factory\PurchaseFactory;
use App\Factory\PurchaseItemFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        UserFactory::createMany(10);
        CategoryFactory::createMany(10, function () {
            return ['owner' => UserFactory::random()];
        });
        ProductFactory::createMany(50, function () {
            return [
                // each Post will have a random Category (chosen from those created above)
                'category' => CategoryFactory::random(),
                // each Post will have between 0 and 6 Tag's (chosen from those created above)
                //'tags' => TagFactory::randomRange(0, 6),

                // each Post will have between 0 and 10 Comment's that are created new
                //'comments' => CommentFactory::new()->many(0, 10),
            ];
        });
        PurchaseFactory::createMany(30, function () {
            return [
                'user' => UserFactory::random(),
                'purchaseItems' => PurchaseItemFactory::new(function () {
                    return ['product' => ProductFactory::random()];
                })->many(1, 5)
            ];
        });
        $manager->flush();
    }
}
