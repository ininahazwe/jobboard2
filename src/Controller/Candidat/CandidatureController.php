<?php

namespace App\Controller\Candidat;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Entity\Messages;
use App\Entity\User;
use App\Form\CandidatureType;
use App\Form\MessagesType;
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

    #[Route('/new', name: 'candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('candidature_index');
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id}/edit', name: 'candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidature_index');
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form->createView(),
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

    #[Route('/postuler/{id}/{type}', name: 'candidature_postuler-externe')]
    public function postuler(Request $request, Annonce $annonce,CandaditureRepository $candaditureRepository, $type, Mailer $mailer): Response
    {
        $candidature = new Candidature();
        if ($type == Candidature::TYPE_MAIL){
            return $this->redirectToRoute('candidature_postuler_email', ['id'=> $annonce->getId()]);
        }

        $hasCandidature = $candaditureRepository->hasCandidature($this->getUser(), $annonce);
        if (!$hasCandidature){

            if ($this->getUser()){
                $candidature->setCandidat($this->getUser());
            }

            $candidature->addAnnonce($annonce);
            $candidature->setEntreprise($annonce->getEntreprise());
            $candidature->setType($type);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();

            $this->addFlash('success', 'Candidature réussie');

            $mailer->send([
                'recipient_email' => $annonce->getAdresseEmail(),
                'subject' => 'Candidature sur l\'offre' . '' . $annonce->getName(),
                'html_template' => 'emails/nouvelle_candidature.html.twig',
                'context' => [
                    'candidature' => $candidature
                ]
            ]);
        }

        if ($type == Candidature::TYPE_EXTERNE){
             return $this->redirect($annonce->getLien());
        }
        return $this->redirectToRoute('annonce_show_unit', ['id'=> $annonce->getId(), 'slug'=> $annonce->getSlug()]);
    }

    #[Route('/email/postuler/{id}', name: 'candidature_postuler_email')]
    public function postulerByEmail(Request $request, Annonce $annonce, CandaditureRepository $candaditureRepository, Mailer $mailer): Response
    {
        $hasCandidature = $candaditureRepository->hasCandidature($this->getUser(), $annonce);
        if ($hasCandidature){
            return $this->redirectToRoute('annonce_show_unit', ['id'=> $annonce->getId(), 'slug'=> $annonce->getSlug()]);
        }
        $candidature = new Candidature();

        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($this->getUser()){
                $candidature->setCandidat($this->getUser());
            }
            $candidature->addAnnonce($annonce);
            $candidature->setEntreprise($annonce->getEntreprise());
            $candidature->setType(Candidature::TYPE_MAIL);
            $recruteur = $entityManager->getRepository(User::class)->findOneBy(['email' => $annonce->getAdresseEmail()]);

            if ($recruteur){
                $candidature->setRecruteur($recruteur);
            }

            $entityManager->persist($candidature);

            $message->setSender($this->getUser());
            $message->setTitre($annonce->getName());
            $message->setEmailSender($this->getUser()->getEmail());

            if ($recruteur){
                $message->setRecipient($recruteur);
            }
            $message->setEmailReceiver($annonce->getAdresseEmail());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Candidature réussie');

            /*$mailer->send([
                'recipient_email' => $annonce->getAdresseEmail(),
                'subject'         => 'Candidature sur l\'offre' . '' . $annonce->getName(),
                'html_template'   => 'emails/nouvelle_candidature.html.twig',
                'context'         => [
                    'candidature' => $candidature
                ]
            ]);*/

            return $this->redirectToRoute('annonce_show_unit', ['id'=> $annonce->getId(), 'slug'=> $annonce->getSlug()]);
        }

        return $this->render('annonce/postuler_email.html.twig', [
            'message' => $message,
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);

    }

    #[Route('/postuler/intern/{id}/{type}', name: 'candidature_postuler_interne')]
    public function postulerInterne(Request $request, Annonce $annonce,CandaditureRepository $candaditureRepository, $type, Mailer $mailer): Response
    {
        $entreprise = $annonce->getEntreprise();
        $candidature = new Candidature();
        if ($entreprise->isRegroupementCandidature()){
            $hasCandidatureEntreprise = $candaditureRepository->hasCandidatureEntreprise($this->getUser(), $entreprise);
            if ($hasCandidatureEntreprise){
                $candidature = $hasCandidatureEntreprise;
            }
        }
        $hasCandidature = $candaditureRepository->hasCandidature($this->getUser(), $annonce);

        if (!$hasCandidature){

            if ($this->getUser()){
                $candidature->setCandidat($this->getUser());
            }

            $candidature->addAnnonce($annonce);
            $candidature->setRecruteur($annonce->getNextRecruteur());
            $candidature->setEntreprise($entreprise);
            $candidature->setType($type);

            $annonce->setCurrentRecruteur($annonce->getNextRecruteur());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($candidature);
            $entityManager->persist($annonce);
            $entityManager->flush();

            $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_CANDIDATURE']);

            $loader = new ArrayLoader([
                'email' => $email->getContent(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email',['candidature' => $candidature]);

            $this->addFlash('success', 'Candidature réussie');

            $mailer->send([
                'recipient_email' => $annonce->getNextRecruteur()->getEmail(),
                'subject'         => $email->getSubject(),
                'html_template'   => 'emails/email_vide.html.twig',
                'context'         => [
                    'message' => $message
                ]
            ]);
        }

        return $this->redirectToRoute('annonce_show_unit', ['id'=> $annonce->getId(), 'slug'=> $annonce->getSlug()]);
    }
}
