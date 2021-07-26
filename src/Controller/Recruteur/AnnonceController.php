<?php

namespace App\Controller\Recruteur;

use App\Entity\Annonce;
use App\Entity\Entreprise;
use App\Entity\Offre;
use App\Form\AnnonceType;
use App\Form\SearchForm;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/annonce')]
class AnnonceController extends AbstractController
{
    #[Route('/index', name: 'annonce_index')]
    public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator): Response
    {
        // Pagination
        $data = $annonceRepository->findAllActiveQuery($this->getUser());

        $annonces = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        //Formulaire de recherche
        $form = $this->createForm(SearchForm::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $annonces = $annonceRepository->search(
                $search->get('mots')->getData()
            );
        }


        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'form' => $form->createView()
            //'entreprises' => $this->getUser()->getEntrepriseAll(),
        ]);
    }

    #[Route('/new', name: 'annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class ,$annonce, [
                'user' => $this->getUser(),
                ]
            );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entreprise = $entityManager->getRepository(Entreprise::class)->find($form->get('entreprise')->getData());

            if ($entreprise->canCreateAnnonce()){

                $entityManager->persist($annonce);
                $entityManager->flush();

                $this->addFlash('success', 'Publication réussie');

                return $this->redirectToRoute('annonce_index', []);
            }else{
                $this->addFlash('warning', 'Vous avez atteint le nombre maximum d\'annonces à publier');
                return $this->redirectToRoute('annonce_index', []);
            }

        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce): Response
    {
        $form = $this->createForm(AnnonceType::class ,$annonce, [
                'user' => $this->getUser(),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->updateTimestamps();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');
            return $this->redirectToRoute('annonce_index');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'annonce_delete')]
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index');
    }
}
