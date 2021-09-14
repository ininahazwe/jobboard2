<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Entreprise;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/annonce')]
class AnnonceController extends AbstractController
{
    #[Route('/index', name: 'annonce_index')]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAllActiveQuery($this->getUser());
        $annoncesArchives = $annonceRepository->getAnnoncesArchivees();
        $annoncesAttente = $annonceRepository->getAnnoncesAttente();
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'archives' => $annoncesArchives,
            'attente' => $annoncesAttente
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

                return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->addFlash('warning', 'Vous avez atteint le nombre maximum d\'annonces à publier');
                return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
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
            return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/activer/{id}', name: 'annonce_activer')]
    public function activer(Annonce $annonce): Response
    {
        $annonce->setIsActive(!$annonce->getIsActive());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();

        return new Response("true");
    }

    #[Route('/favoris/ajout/{id}', name: 'annonce_ajout_favoris')]
    public function ajoutFavoris(Annonce $annonce): RedirectResponse
    {
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('annonces_show_all');
    }

    #[Route('/favoris/retrait/{id}', name: 'annonce_retrait_favoris')]
    public function retraitFavoris(Annonce $annonce): RedirectResponse
    {
        if(!$annonce){
            throw new NotFoundHttpException('Pas d\'annonce trouvée');
        }
        $annonce->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return $this->redirectToRoute('annonces_show_all');
    }

    #[Route('/selection', name: 'annonce_favoris')]
    public function showFavoris(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAnnoncesEnFavori($this->getUser());

        return $this->render('annonce/favoris.html.twig', [
            'annonces' => $annonces,
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
