<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use App\Entity\Purchase;
use App\Cart\CartItem;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Service\Persistence\GlobalDataPersister;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    /** @var CartService */
    protected $cartService;
    /** @var GlobalDataPersister */
    protected $persister;

    public function __construct(CartService $cartService, GlobalDataPersister $persister)
    {
        $this->cartService = $cartService;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm",name="purchase_confirm")
     * @IsGranted("ROLE_USER")
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash("warning", "Vous devez remplir le formulaire de confirmation");
            return $this->redirectToRoute("cart_show");
        }

        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash("warning", "Votre panier est vide");
            return $this->redirectToRoute("cart_show");
        }

        /** @var User */
        $user = $this->getUser();

        /** @var Purchase */
        $purchase = $form->getData();
        $this->persister->persist($purchase);

        // $this->cartService->empty();
        // $this->addFlash('success', "La commande a bien été enregistrée");
        return $this->redirectToRoute("purchase_payment_form", [
            'id' => $purchase->getId()
        ]);
    }
}
