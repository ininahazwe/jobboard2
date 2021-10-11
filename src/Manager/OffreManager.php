<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Offre;
use App\Entity\User;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OffreManager
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var StripeService
     */
    protected StripeService $stripeService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StripeService $stripeService
     */
    public function __construct(
            EntityManagerInterface $entityManager,
            StripeService $stripeService
    ) {
        $this->em = $entityManager;
        $this->stripeService = $stripeService;
    }

    public function getOffres(): array {
        return $this->em->getRepository(Offre::class)
                ->findAll();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function countSoldeOrder(User $user): mixed {
        return $this->em->getRepository(Order::class)
                ->countSoldeOrder($user);
    }

    public function getOrders(User $user)
    {
        return $this->em->getRepository(Order::class)
                ->findByUser($user);
    }

    /**
     * @throws ApiErrorException
     */
    public function intentSecret(Offre $offre)
    {
        $intent = $this->stripeService->paymentIntent($offre);

        return $intent['client_secret'] ?? null;
    }

    /**
     * @param array $stripeParameter
     * @param Offre $offre
     * @return array|null
     * @throws ApiErrorException
     */
    public function stripe(array $stripeParameter, Offre $offre): ?array {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $offre);

        if($data) {
            $resource = [
                    'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                    'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                    'stripeId' => $data['charges']['data'][0]['id'],
                    'stripeStatus' => $data['charges']['data'][0]['status'],
                    'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    /**
     * @param array $resource
     * @param Offre $offre
     * @param User $user
     */
    public function create_subscription(array $resource, Offre $offre, User $user)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setOffre($offre);
        $order->setPrice($offre->getPrix());
        $order->setReference(uniqid('', false));
        $order->setBrandStripe($resource['stripeBrand']);
        $order->setLast4Stripe($resource['stripeLast4']);
        $order->setIdChargeStripe($resource['stripeId']);
        $order->setStripeToken($resource['stripeToken']);
        $order->setStatusStripe($resource['stripeStatus']);
        $order->setUpdatedAt(new \Datetime());
        $order->setCreatedAt(new \Datetime());
        $this->em->persist($order);
        $this->em->flush();
    }
}