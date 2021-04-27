<?php

namespace App\Controller\Purchase;

use Stripe\Stripe;
use App\Entity\Purchase;
use App\Stripe\StripeService;
use Stripe\PaymentIntent;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /** @var StripeService */
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * @Route("/purchase/pay/{id<\d+>}",name="purchase_payment_form")
     * @IsGranted("CAN_EDIT", subject="purchase")
     */
    public function showCardForm(Purchase $purchase)
    {
        $paymentIntent = $this->stripeService->getPaymentIntent($purchase->getTotal());
        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'stripePublicKey' => $this->stripeService->getPublicKey(),
            'purchase' => $purchase
        ]);
    }
}
