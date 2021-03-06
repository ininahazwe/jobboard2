<?php

namespace App\Controller;

use App\Entity\Dictionnaire;
use App\Form\DictionnaireType;
use App\Repository\DictionnaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/dictionnaire')]
class DictionnaireController extends AbstractController
{
    #[Route('/', name: 'dictionnaire_index', methods: ['GET'])]
    public function index(DictionnaireRepository $dictionnaireRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN_HANDICV');

        $dictionnaire = $dictionnaireRepository->findAll();

        return $this->render('dictionnaire/index.html.twig', [
            'dictionnaires' => $dictionnaire,
        ]);
    }

    #[Route('/new', name: 'dictionnaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN_HANDICV');

        $dictionnaire = new Dictionnaire();
        $form = $this->createForm(DictionnaireType::class, $dictionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dictionnaire);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('dictionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dictionnaire/new.html.twig', [
            'dictionnaire' => $dictionnaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'dictionnaire_show', methods: ['GET'])]
    public function show(Dictionnaire $dictionnaire): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN_HANDICV');

        return $this->render('dictionnaire/show.html.twig', [
            'dictionnaire' => $dictionnaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'dictionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dictionnaire $dictionnaire): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN_HANDICV');

        $form = $this->createForm(DictionnaireType::class, $dictionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dictionnaire->updateTimestamps();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('dictionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dictionnaire/edit.html.twig', [
            'dictionnaire' => $dictionnaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'dictionnaire_delete', methods: ['POST'])]
    public function delete(Request $request, Dictionnaire $dictionnaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dictionnaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($dictionnaire);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussie');
        }

        return $this->redirectToRoute('dictionnaire_index');
    }
}
