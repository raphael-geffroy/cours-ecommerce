<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session  = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function setCart(array $cart): void
    {
        $this->session->set('cart', $cart);
    }

    public function add(int $id): void
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) $cart[$id] = 0;
        $cart[$id]++;
        $this->setCart($cart);
    }

    public function remove(int $id): void
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->setCart($cart);
    }

    public function decrement(int $id): void
    {
        $cart = $this->getCart();
        $cart[$id]--;
        if ($cart[$id] == 0) unset($cart[$id]);
        $this->setCart($cart);
    }

    public function empty(): void
    {
        $this->setCart([]);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) continue;
            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * @return array<CartItem>
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) continue;
            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }
}
