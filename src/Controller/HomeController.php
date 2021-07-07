<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Annuaire;
use App\Entity\Menu;
use App\Entity\Page;
use App\Repository\AgendaRepository;
use App\Repository\AnnonceRepository;
use App\Repository\AnnuaireRepository;
use App\Repository\CandaditureRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\MenuRepository;
use App\Repository\ModeleOffreCommercialeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function index(AnnonceRepository $annoncesRepo, Request $request, ModeleOffreCommercialeRepository $modeleOffreCommercialeRepository): Response
    {
        $annonces = $annoncesRepo->findActiveAndLive(5);
        $offres = $modeleOffreCommercialeRepository->findAll();

        return $this->render('home/index.html.twig', [
            'annonces' => $annonces,
            'modeles' => $offres,
        ]);
    }
    #[Route('/view/menus', name:'app_view_menus')]

    public function menuIndex(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->getAllMenus();
        return $this->render('layouts/_header.html.twig', [
            'menus' => $menus,
            'menuRepository' => $menuRepository
        ]);
    }

    #[Route('/view/footer', name:'app_view_footer')]
    public function footerIndex(MenuRepository $menuRepository): Response
    {
        $footers = $menuRepository->getAllFooters();
        return $this->render('layouts/_footer.html.twig', [
            'footers' => $footers,
            'contenu' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_CONTENU]),
            'presentationRs' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_PRESENTATION_RS]),
            'twitter' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_TWITTER]),
            'facebook' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_FACEBOOK]),
            'instagram' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_INSTAGRAM]),
            'linkedin' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_LINKEDIN]),
            'skype' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_SKYPE]),
            'youtube' => $menuRepository->findOneBy(['type' => Menu::TYPE_MENU_YOUTUBE]),
            'menuRepository' => $menuRepository
        ]);
    }

    /*Affichage des offres d'emploi*/
    #[Route('/offres', name: 'annonces_show_all', methods: ['GET'])]
    public function showAllAnnonces(AnnonceRepository $annonceRepository, Request $request): Response
    {
        $annonces = $annonceRepository->findActiveAndLive();

        return $this->render('annonce/showAll.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/offres/{id}-{slug}', name: 'annonce_show_unit', methods: ['GET'])]
    public function showAnnonce($slug, $id,
                                AnnonceRepository $annonceRepository,
                                CandaditureRepository $candaditureRepository,
                                Request $request): Response
    {
        $annonce = $annonceRepository->findOneBy(['slug' => $slug, 'id' => $id]);

        $hasCandidature = false;
        if ($this->getUser()){
            $hasCandidature = $candaditureRepository->hasCandidature($this->getUser(), $annonce);
        }


        return $this->render('annonce/show_unit.html.twig', [
            'annonce' => $annonce,
            'hasCandidature' => $hasCandidature
        ]);
    }

    /*Affichage des entreprises*/
    #[Route('/entreprises', name: 'entreprise_show_all', methods: ['GET'])]
    public function showAllEntreprises(EntrepriseRepository $entrepriseRepository, Request $request, ): Response
    {
        $entreprises = $entrepriseRepository->findAll();

        return $this->render('entreprise/showAll.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/entreprises/{id}-{slug}', name: 'entreprise_show_unit', methods: ['GET'])]
    public function showEntreprise($id, $slug, EntrepriseRepository $entrepriseRepository, Request $request): Response
    {
        $entreprise = $entrepriseRepository->findOneBy(['slug' => $slug, 'id' => $id]);

        return $this->render('entreprise/show_unit.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    /*Affichage des agenda*/
    #[Route('/agenda', name: 'agenda_show_all', methods: ['GET'])]
    public function ShowAllAgendas(AgendaRepository $agendaRepository): Response
    {
        return $this->render('agenda/showAll.html.twig', [
            'agendas' => $agendaRepository->findAll(),
        ]);
    }

    #[Route('/agenda/{id}-{slug}', name: 'agenda_show_unit', methods: ['GET'])]
    public function showAgenda($id, $slug, AgendaRepository $agendaRepository, Request $request): Response
    {
        $agenda = $agendaRepository->findOneBy(['slug' => $slug, 'id' => $id]);

        return $this->render('agenda/show_unit.html.twig', [
            'agenda' => $agenda,
        ]);
    }

    /*Affichage de l'annuaire*/
    #[Route('/annuaire', name: 'annuaire_show_all', methods: ['GET'])]
    public function showAllAnnuaire(AnnuaireRepository $annuaireRepository): Response
    {
        return $this->render('annuaire/showAll.html.twig', [
            'annuaires' => $annuaireRepository->findAll(),
        ]);
    }

    #[Route('/annuaire/{id}-{slug}', name: 'annuaire_show_unit', methods: ['GET'])]
    public function showAnnuaire($id, $slug, AnnuaireRepository $annuaireRepository): Response
    {
        $annuaire = $annuaireRepository->findOneBy(['slug' => $slug, 'id' => $id]);

        return $this->render('annuaire/show_unit.html.twig', [
            'annuaire' => $annuaire,
        ]);
    }

    #[Route('/{slug}', name:'page_show')]
    public function page($slug): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $page = $entityManager->getRepository(Page::class)->findOneBy(['slug' => $slug]);
        if(!$page){
            return $this->redirect($this->generateUrl('app_home'));
        }
        return $this->render('page/show.html.twig', [
            'page' => $page
        ]);
    }
}
