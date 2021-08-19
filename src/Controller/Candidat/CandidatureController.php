<?php

namespace App\Controller\Candidat;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Entity\Messages;
use App\Entity\User;
use App\Form\CandidatureType;
use App\Form\MessagesType;
use App\Repository\AnnonceRepository;
use App\Repository\CandaditureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[Route('/cms/candidature')]
class CandidatureController extends AbstractController
{
    #[Route('/', name: 'candidature_index', methods: ['GET'])]
    public function index(CandaditureRepository $candaditureRepository): Response
    {
        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candaditureRepository->getAllCandidatures($this->getUser()),
        ]);
    }

    #[Route('/{id}', name: 'candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id}', name: 'candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidature_index');
    }


}
