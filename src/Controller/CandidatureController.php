<?php

namespace App\Controller;

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
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('candidature/index.html.twig', [
            'candidatures' => $candaditureRepository->getAllCandidatures($this->getUser()),
        ]);
    }

    #[Route('/acceptees', name: 'candidature_acceptees', methods: ['GET'])]
    public function acceptees(CandaditureRepository $candaditureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        return $this->render('candidature/acceptees.html.twig', [
            'candidatures' => $candaditureRepository->getCandidaturesAcceptees($this->getUser()),
        ]);
    }

    #[Route('/refusees', name: 'candidature_refusees', methods: ['GET'])]
    public function refusees(CandaditureRepository $candaditureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        return $this->render('candidature/refusees.html.twig', [
            'candidatures' => $candaditureRepository->getCandidaturesRefusees($this->getUser()),
        ]);
    }

    #[Route('/en-attente', name: 'candidature_attente', methods: ['GET'])]
    public function attente(CandaditureRepository $candaditureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        return $this->render('candidature/attente.html.twig', [
            'candidatures' => $candaditureRepository->getCandidaturesAttente($this->getUser()),
        ]);
    }

    #[Route('/{id}', name: 'candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/accepter/{id}', name: 'candidature_accepter')]
    public function accepter(Request $request, Candidature $candaditure): Response
    {
        $candaditure->setStatut(!$candaditure->getStatut());

        $em = $this->getDoctrine()->getManager();
        $em->persist($candaditure);
        $em->flush();

        $this->addFlash('success', 'Candidature acceptée');

        if ($referer = $request->get('referer', false)) {
            $referer = base64_decode($referer);
            return $this->redirect(($referer));
        } else {
            return $this->redirectToRoute('candidature_index');
        }
    }

    #[Route('/refuser/{id}', name: 'candidature_refuser')]
    public function refuser(Request $request, Candidature $candaditure): Response
    {
        $candaditure->setStatut(!$candaditure->getStatut());

        $em = $this->getDoctrine()->getManager();
        $em->persist($candaditure);
        $em->flush();

        $this->addFlash('danger', 'Refus enregistré');

        if ($referer = $request->get('referer', false)) {
            $referer = base64_decode($referer);
            return $this->redirect(($referer));
        } else {
            return $this->redirectToRoute('candidature_index');
        }
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
