<?php

namespace App\EventDispatcher;

use App\Entity\Purchase;
use Symfony\Component\Mime\Email;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\RawMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSubscriber implements EventSubscriberInterface
{
    protected MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'onSuccess'
        ];
    }
    public function onSuccess(PurchaseSuccessEvent $event)
    {
        $purchase = $event->getPurchase();
        $purchase->getPurchaseItems();
        $email = new TemplatedEmail();
        $email->from(new Address("contact@mail.com", "Contact SymShop"))
            ->to(new Address($purchase->getUser()->getEmail(), "{$purchase->getUser()->getLastname} {$purchase->getUser()->getFirstname}"))
            ->subject("Commande n°{$purchase->getId()} créée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context(['purchase' => $purchase]);
        $this->mailer->send($email);
    }
}
