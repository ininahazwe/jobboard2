<?php

namespace App\Controller;

use App\Service\CallVillesApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleApiController extends AbstractController
{
    #[Route('/ville', name: 'ville_api')]
    public function index(CallVillesApi $callVillesApi): Response
    {

        return $this->render('api/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
