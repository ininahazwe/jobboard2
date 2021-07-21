<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Repository\OffreRepository;
use App\Service\Mailer;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;

#[Route('/cms/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'facture_index', methods: ['GET'])]
    public function index(Request $request, FactureRepository $factureRepository, PaginatorInterface $paginator): Response
    {
        $data = $factureRepository->findAll();
        $factures = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
        ]);
    }

    /**
     * @param FactureRepository $factureRepository
     * @return Response
     */
    #[Route('/apayer', name: 'facture_a_payer_index', methods: ['GET'])]
    public function factureAPayer(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/apayer.html.twig', [
            'factures' => $factureRepository->getFacturesAPayer(),
        ]);
    }

    /**
     * @param FactureRepository $factureRepository
     * @return Response
     */
    #[Route('/payees', name: 'facture_payees_index', methods: ['GET'])]
    public function facturePayees(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/payees.html.twig', [
            'factures' => $factureRepository->getFacturesPayees(),
        ]);
    }

    /**
     * @param FactureRepository $factureRepository
     * @return Response
     */
    #[Route('/critiques', name: 'facture_critiques_index', methods: ['GET'])]
    public function facturesCritiques(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/critiques.html.twig', [
            'factures' => $factureRepository->getFacturesAPayer(true),
        ]);
    }

    /**
     * @param OffreRepository $offreRepository
     * @param Mailer $mailer
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    #[Route('/generate', name: 'factures_generate')]
    public function generateFactures(OffreRepository $offreRepository, Mailer $mailer): Response
    {
        $tva = 20;
        $entityManager = $this->getDoctrine()->getManager();

        $offres = $offreRepository->getOffresActiveNotFactured();
        $entreprises = new ArrayCollection();
        foreach ($offres as $offre){
            if (!$entreprises->contains($offre->getEntreprise())){
                $entreprises->add($offre->getEntreprise());
            }
        }

        foreach ($entreprises as $entreprise){
            $duree = "+1 month";
            $dateFin = new \DateTime('now' . $duree);

            $offresNotFactured = $offreRepository->getOffreActiveNotFactured($entreprise);
            $ref = $offreRepository->genererRef();

            $prix = $offreRepository->getOffreActiveNotFactured($entreprise, true);
            $prixTVA = $prix * $tva / 100;
            $prixTTC = $prix + $prixTVA;

            $facture = new Facture();
            $facture->setEntreprise($entreprise);
            $facture->setReference($ref);
            $facture->setPrix($prix);
            $facture->setTVA($tva);
            $facture->setCreatedAt(new \DateTime('now'));
            $facture->setPrixTTC($prixTTC);
            $facture->setIsPaid(false);
            $facture->setLimiteDatePaid($dateFin);
            $entityManager->persist($facture);

            foreach($offresNotFactured as $_offre){
                $_offre->setIsFacture(true);
                $_offre->setFacture($facture);
                $entityManager->persist($_offre);

            }

            $entityManager->flush();

            foreach($entreprise->getSuperRecruteurs() as $user)
            {
                $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_GENERATION_FACTURE']);

                $loader = new ArrayLoader([
                    'email' => $email->getContent(),
                ]);

                $twig = new Environment($loader);
                $message = $twig->render('email',['user' =>$user, 'factures' => $facture, 'entreprises' => $entreprises, 'offres' => $offres ]);

                $this->addFlash('success', 'Candidature réussie');

                $mailer->send([
                    'recipient_email' => $user->getEmail(),
                    'subject'         => $email->getSubject(),
                    'html_template'   => 'emails/email_vide.html.twig',
                    'context'         => [
                        'message' => $message
                    ]
                ]);
            }
        }

        $this->addFlash('success', 'La génération des factures a été faite avec succès');

        return $this->redirectToRoute('facture_index');
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/new', name: 'facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($facture);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Facture $facture
     * @return Response
     */
    #[Route('/{id}', name: 'facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    /**
     * @param Request $request
     * @param Facture $facture
     * @return Response
     */
    #[Route('/{id}/edit', name: 'facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Facture $facture
     * @return Response
     */
    #[Route('/{id}', name: 'facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($facture);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussie');
        }

        return $this->redirectToRoute('facture_index');
    }

    /**
     * @param Facture $facture
     * @return Response
     */
    #[Route('/{id}/facture-download', name: 'facture_download')]
    public function factureDataDownload(Facture $facture): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        //Instanciation de DomPdf
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);

        // html
        $html = $this->renderView('facture/facture_download.html.twig', [
            'facture' => $facture,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // nom du fichier
        $fichier = 'facture-'. $facture->getEntreprise() . '-' . $facture . '.pdf';

        // Envoi du PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => false
        ]);

        //return new Response();
    }

    /**
     * @param Request $request
     * @param Facture $facture
     * @param Mailer $mailer
     * @return Response
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/{id}/reglement', name: 'facture_reglement')]
    public function reglement(Request $request, Facture $facture, Mailer $mailer): Response
    {
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $date = new DateTime($request->get('date') . " 00:00:00") ;
        $payment = $request->get('choixPaiement');

        $facture->setPaymentDate($date);
        $facture->setPaymentMethods($payment);
        $facture->setLimiteDatePaid(null);
        $facture->setIsPaid(true);
        $entityManager->persist($facture);
        $entityManager->flush();

        $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_REGLEMENT_FACTURE']);

        $loader = new ArrayLoader([
            'email' => $email->getContent(),
        ]);

        $twig = new Environment($loader);
        $message = $twig->render('email',['facture' => $facture]);

        $this->addFlash('success', 'Candidature réussie');

        $mailer->send([
            'recipient_email' => $user->getEmail(),
            'subject'         => $email->getSubject(),
            'html_template'   => 'emails/email_vide.html.twig',
            'context'         => [
                'message' => $message
            ]
        ]);

        $this->addFlash('success', 'Reglèment enregistré');

        return $this->redirectToRoute('facture_index');
    }
}
