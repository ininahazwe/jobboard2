<?php

namespace App\Controller;

use App\Entity\Annuaire;
use App\Form\AnnuaireType;
use App\Repository\AnnuaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/annuaire')]
class AnnuaireController extends AbstractController
{
    #[Route('/', name: 'annuaire_index', methods: ['GET'])]
    public function index(AnnuaireRepository $annuaireRepository): Response
    {
        return $this->render('annuaire/index.html.twig', [
            'annuaires' => $annuaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'annuaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        $annuaire = new Annuaire();
        $form = $this->createForm(AnnuaireType::class, $annuaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annuaire);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('annuaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annuaire/new.html.twig', [
            'annuaire' => $annuaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'annuaire_show', methods: ['GET'])]
    public function show(Annuaire $annuaire): Response
    {
        return $this->render('annuaire/show.html.twig', [
            'annuaire' => $annuaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'annuaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annuaire $annuaire): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        $form = $this->createForm(AnnuaireType::class, $annuaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annuaire->updateTimestamps();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('annuaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annuaire/edit.html.twig', [
            'annuaire' => $annuaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'annuaire_delete', methods: ['POST'])]
    public function delete(Request $request, Annuaire $annuaire): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        if ($this->isCsrfTokenValid('delete'.$annuaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annuaire);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussie');
        }

        return $this->redirectToRoute('annuaire_index');
    }
}
