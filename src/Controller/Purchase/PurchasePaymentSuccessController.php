<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Persistence\GlobalDataPersister;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /** @var CartService */
    protected $cartService;
    /** @var GlobalDataPersister */
    protected $persister;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(CartService $cartService, GlobalDataPersister $persister, EventDispatcherInterface $eventDispatcher)
    {
        $this->cartService = $cartService;
        $this->persister = $persister;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @Route("/purchase/terminate/{id<\d+>}",name="purchase_payment_success")
     * @IsGranted("CAN_EDIT", subject="purchase")
     */
    public function success(Purchase $purchase)
    {
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->persister->flush();
        $this->cartService->empty();

        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $this->eventDispatcher->dispatch($purchaseEvent, 'purchase.success');

        $this->addFlash("success", "La commande a été payée et validée");
        return $this->redirectToRoute("purchase_index");
    }
}
