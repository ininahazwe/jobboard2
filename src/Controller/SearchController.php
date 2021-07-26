<?php

namespace App\Controller;

use App\Form\SearchAnnonceAdvancedType;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/annonce/search', name: 'search_annonce')]
    public function searchWithFilters(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $annonces = [];
        $searchAnnonceForm = $this->createForm(SearchAnnonceAdvancedType::class);

        if ($searchAnnonceForm->handleRequest($request)->isSubmitted() && $searchAnnonceForm->isValid()){
            $criteria = $searchAnnonceForm->getData();
            $annonces = $annonceRepository->searchAnnoncesAdvanced($criteria);
        }

        return $this->render('annonce/search.html.twig', [
            'search_form'=> $searchAnnonceForm->createView(),
            'annonces' => $annonces
        ]);
    }
}