<?php

namespace App\Service;

use App\Entity\Offre;
use App\Entity\Order;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeService
{
    private $privateKey;

    public function __construct()
    {
        if($_ENV['APP_ENV'] === 'dev'){
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        }else{
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    /**
     * @param Offre $offre
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function paymentIntent(Offre $offre): PaymentIntent {
        Stripe::setApiKey($this->privateKey);

        return PaymentIntent::create([
            'amount' => $offre->getPrix() * 100,
            'currency' => Order::DEVISE,
            'payment_method_types' => ['card']
        ]);
    }

    /**
     * @param $amount
     * @param $currency
     * @param $description
     * @param array $stripeParameter
     * @return PaymentIntent|null
     * @throws ApiErrorException
     */
    public function paiement($amount, $currency, $description, array $stripeParameter): ?PaymentIntent {
        Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])){
            $payment_intent = PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if($stripeParameter['stripeIntentStatus'] === 'succeeded'){

        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    /**
     * @param array $stripeParameter
     * @param Offre $offre
     * @return PaymentIntent|null
     * @throws ApiErrorException
     */
    public function stripe(array $stripeParameter, Offre $offre): ?PaymentIntent {
        return $this->paiement(
            $offre->getPrix() * 100,
            Order::DEVISE,
            $offre->getFormule(),
                $stripeParameter
        );
    }
}