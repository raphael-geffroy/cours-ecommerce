<?php

namespace App\Controller;

use App\Entity\Product;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /** @var CartService */
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart/add/{id<\d+>}", name="cart_add")
     */
    public function add(Product $product, Request $request)
    {
        $this->cartService->add($product->getId());
        if ($request->query->get('returnToCart')) return $this->redirectToRoute('cart_show');
        $this->addFlash('success', 'Le produit a été ajouté au panier');
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
        //$this->json('', 204);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delete/{id<\d+>}", name="cart_delete")
     */
    public function delete(Product $product)
    {
        $this->cartService->remove($product->getId());
        $this->addFlash("success", "Le produit a bien été supprimé du panier");
        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id<\d+>}", name="cart_decrement")
     */
    public function decrement(Product $product)
    {
        $this->cartService->decrement($product->getId());
        //$this->addFlash("success", "Le produit a bien été supprimé du panier");
        return $this->redirectToRoute("cart_show");
    }
}
