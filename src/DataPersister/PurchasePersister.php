<?php

namespace App\DataPersister;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{

    /** @var EntityManagerInterface */
    protected $em;
    /** @var CartService */
    protected $cartService;
    /** @var Security */
    protected $security;

    public function __construct(EntityManagerInterface $em, CartService $cartService, Security $security)
    {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Purchase;
    }

    public function persist($data, array $context = [])
    {
        foreach ($this->cartService->getDetailedCartitems() as $cartItem) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->quantity)
                ->setTotal($cartItem->getTotal());
            $data->addPurchaseItem($purchaseItem);
        }
        $data->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->em->remove($data);
        $this->em->flush();
    }
}
