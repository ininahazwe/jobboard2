<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Annuaire;
use App\Entity\Blog;
use App\Entity\Email;
use App\Entity\Entreprise;
use App\Entity\Menu;
use App\Entity\Page;
use App\Entity\User;
use App\Form\ContactType;
use App\Form\CreationEntrepriseType;
use App\Form\EntrepriseType;
use App\Form\SearchAnnonceForm;
use App\Form\SearchEntrepriseForm;
use App\Form\UserType;
use App\Repository\AgendaRepository;
use App\Repository\AnnonceRepository;
use App\Repository\AnnuaireRepository;
use App\Repository\CandaditureRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\MenuRepository;
use App\Repository\ModeleOffreCommercialeRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function index(AnnonceRepository $annoncesRepo, Request $request, ModeleOffreCommercialeRepository $modeleOffreCommercialeRepository, EntrepriseRepository $entrepriseRepository): Response
    {
        $annonces = $annoncesRepo->findActiveAndLive(5);
        $offres = $modeleOffreCommercialeRepository->findAll();
        $entreprises = $entrepriseRepository->getEntrepriseHome(6);

        return $this->render('home/index.html.twig', [
            'annonces' => $annonces,
            'modeles' => $offres,
            'entreprises' => $entreprises
        ]);
    }

    #[Route('/recruteur', name:'app_home_recruteur')]
    public function indexRecruteur(AnnonceRepository $annoncesRepo,
                                   Request $request,
                                   ModeleOffreCommercialeRepository $modeleOffreCommercialeRepository,
                                   EntrepriseRepository $entrepriseRepository,
                                   Mailer $mailer): Response
    {
        $annonces = $annoncesRepo->findActiveAndLive(5);
        $offres = $modeleOffreCommercialeRepository->findAll();
        $entreprises = $entrepriseRepository->getEntrepriseHome(6);

        //formulaire de contact
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();

            $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_CONTACT']);

            $loader = new ArrayLoader([
                'email' => $email->getContent(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email',['user' => $this->getUser(), 'contact' => $contact ]);

            $mailer->send([
                'recipient_email' => 'contact@talents-handicap.com',
                'subject'         => $email->getSubject(),
                'html_template'   => 'emails/email_vide.html.twig',
                'context'         => [
                    'message' => $message
                ]
            ]);

            $this->addFlash('success', 'Envoi réussi');

            $this->redirectToRoute('app_home_recruteur');
        }

        return $this->render('home/index_recruteur.html.twig', [
            'annonces' => $annonces,
            'modeles' => $offres,
            'entreprises' => $entreprises,
            'contactForm' => $form->createView()
        ]);
    }

    #[Route('/view/recruteur/menus', name:'app_view_recruteur_menus')]
    public function menuRecruteurIndex(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->getAllMenus(Menu::TYPE_MENU_RECRUTEUR);
        return $this->render('layouts/_header_recruteur.html.twig', [
            'menus' => $menus,
            'menuRepository' => $menuRepository
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
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchAnnonceForm::class, $data);
        $form->handleRequest($request);

        $annonces = $annonceRepository->findSearch($data);

        return $this->render('annonce/showAll.html.twig', [
            'annonces' => $annonces,
            'form' => $form->createView()
        ]);
    }

    #[Route('/offres/{id}-{slug}', name: 'annonce_show_unit', methods: ['GET'])]
    public function showAnnonce($slug, $id, AnnonceRepository $annonceRepository, CandaditureRepository $candaditureRepository, Request $request): Response
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
        $entreprises = $entrepriseRepository->getEntrepriseActive();

        //Formulaire de recherche
        $form = $this->createForm(SearchEntrepriseForm::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entreprises = $entrepriseRepository->search(
                $search->get('mots')->getData()
            );
        }

        return $this->render('entreprise/showAll.html.twig', [
            'entreprises' => $entreprises,
            'form' => $form->createView()
        ]);
    }

    #[Route('/entreprises/{id}-{slug}', name: 'entreprise_show_unit', methods: ['GET'])]
    public function showEntreprise($id, $slug, EntrepriseRepository $entrepriseRepository, Request $request, AnnonceRepository $annonceRepository): Response
    {
        $entreprise = $entrepriseRepository->findOneBy(['slug' => $slug, 'id' => $id]);
        $annonces = $annonceRepository->getAnnoncesEntreprise($entreprise);

        return $this->render('entreprise/show_unit.html.twig', [
            'entreprise' => $entreprise,
            'annonces' => $annonces
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
            'annuaires' => $annuaireRepository->sortAlphabetically(),
        ]);
    }

    #[Route('/annuaire/{slug}', name: 'annuaire_show_unit', methods: ['GET'])]
    public function showAnnuaire(Annuaire $annuaire): Response
    {
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

        $articles = null;
        if($slug == 'actualites-du-handicap'){
            $articles = $entityManager->getRepository(Blog::class)->findAll();
        }

        $conseils = null;
        if($slug == 'conseils-carriere'){
            $conseils = $entityManager->getRepository(Blog::class)->getConseilsCarriereBlog();
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
            'articles' => $articles,
            'conseils' => $conseils
        ]);
    }

    #[Route('/actualites-du-handicap/articles/{slug}', name:'article_show')]
    public function article($slug): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articles = $entityManager->getRepository(Blog::class)->findOneBy(['slug' => $slug]);
        if(!$articles){
            return $this->redirect($this->generateUrl('app_home'));
        }

        return $this->render('blog/show_unit.html.twig', [
            'blog' => $articles,
        ]);
    }

    #[Route('/recruteur/{slug}', name:'page_show_recruteur')]
    public function pageRecruteur($slug): Response
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

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, Mailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();



            $this->addFlash('success', 'Your message has been sent');

            $this->redirectToRoute('app_home_recruteur');
        }
        return $this->render('home/_contact.html.twig',[
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param ModeleOffreCommercialeRepository $modeleOffreCommercialeRepository
     * @param EntrepriseRepository $entrepriseRepository
     * @param Mailer $mailer
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    #[Route('/create/entreprise', name: 'entreprise_creation', methods: ['GET', 'POST'])]
    public function createEntreprise(Request $request,
                                     EntrepriseRepository $entrepriseRepository,
                                     Mailer $mailer,
                                     UserPasswordEncoderInterface $passwordEncoder,
                                     UserRepository $userRepository
    ): Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(CreationEntrepriseType::class);
        $form->handleRequest($request);
        $ref = $entrepriseRepository->genererRef();

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise->setName($form->get('entreprise')->getData());
            $entreprise->setDescription($form->get('description')->getData());
            $entreprise->setAddress($form->get('address')->getData());
            $entreprise->setCity($form->get('city')->getData());
            $entreprise->setZipcode($form->get('zipcode')->getData());
            $entreprise->setSecteur($form->get('secteur')->getData());
            $entreprise->setNumeroSiren($form->get('siren')->getData());
            $entreprise->setNumeroSiret($form->get('siret')->getData());
            $entreprise->setTaille($form->get('taille')->getData());

            $entreprise->setModeration(Entreprise::EN_ATTENTE);

            $entreprise->setRefClient($ref);

            $entityManager = $this->getDoctrine()->getManager();

            $user = new User();

            $user->setEmail($form->get('email')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());
            $user->setModeration(User::EN_ATTENTE);
            $user->setRoles(['ROLE_SUPER_RECRUTEUR']);
            $password = $userRepository->genererMDP();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $password
                )
            );

            $entityManager->persist($user);

            $entreprise->addSuperRecruteur($user);

            $entityManager->persist($entreprise);

            $entityManager->flush();

            $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_ENREGISTREMENT_ENTREPRISE']);

            $loader = new ArrayLoader([
                'email' => $email->getContent(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email',['user' => $this->getUser(), 'entreprise' => $entreprise ]);

            $mailer->send([
                'recipient_email' => 'contact@talents-handicap.com',
                'subject'         => $email->getSubject(),
                'html_template'   => 'emails/email_vide.html.twig',
                'context'         => [
                    'message' => $message
                ]
            ]);

            return $this->redirectToRoute('entreprise_create_return', ['slug' => $entreprise->getSlug(), 'id' => $user->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('entreprise/create.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create/entreprise/{slug}/{id}', name: 'entreprise_create_return', methods: ['GET'])]
    public function showEntrepriseCreated($slug, $id, EntrepriseRepository $entrepriseRepository, UserRepository $userRepository): Response
    {
        $entreprise = $entrepriseRepository->findOneBy(['slug' => $slug]);
        $user = $userRepository->findOneBy(['id' => $id]);

        return $this->render('entreprise/create_return.html.twig', [
            'entreprise' => $entreprise,
            'user' => $user

        ]);
    }
}
