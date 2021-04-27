<?php

namespace App\Stripe;

class StripeService
{
    protected $secretKey;
    protected $publicKey;

    public function __construct(string $publicKey, string $secretKey)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function  getPaymentIntent(int $amount)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);
        return \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
        ]);
    }
}
