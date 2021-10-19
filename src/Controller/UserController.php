<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\AgendaRepository;
use App\Repository\AnnonceRepository;
use App\Repository\BlogRepository;
use App\Repository\CandaditureRepository;
use App\Repository\EntrepriseRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[Route('/cms/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(EntrepriseRepository $entrepriseRepository,
                          BlogRepository $blogRepository,
                          AnnonceRepository $annonceRepository,
                          CandaditureRepository $candaditureRepository,
                          AgendaRepository $agendaRepository,
                          ChartBuilderInterface $chartBuilder

    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $entreprises = $entrepriseRepository->getEntrepriseActive();
        $blogs =$blogRepository->getBlogLimitedDashBoard();
        $annonces = $annonceRepository->findActiveAndLive();
        $candidatures =$candaditureRepository->findAll();
        $agendas =$agendaRepository->getAgendasEnCours();
        $entityManager = $this->getDoctrine()->getManager();

      $labelsAnnonces = [];
      $dataAnnonces = [];

      $labelsCandidatures = [];
      $dataCandidatures = [];

        //Chart nombre annonces par entreprise

        foreach($entreprises as $entreprise){
          if ($entreprise->getNumberAnnonces() > 0){
            $labelsAnnonces[] = $entreprise->getName();
            $dataAnnonces[] = $entreprise->getNumberAnnonces();
          }
        }

        $chartAnnonces = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chartAnnonces->setData([
            'labels' => $labelsAnnonces,
            'datasets' => [
                [
                    'label' => 'Annonces publiées',
                    'backgroundColor' => 'rgb(237, 253, 246)',
                    'borderColor' => 'rgb(21, 163, 98)',
                    'data' => $dataAnnonces,
                ],
            ],
        ]);
        $chartAnnonces->setOptions([
            'scales' => [
              'yAxes' => [
                'ticks' => [
                  'precision' => 0,
                  'beginAtZero' => true,
                ]
              ]
            ]
        ]);

        //Chart candidatures reçues par entreprise

        foreach($entreprises as $entreprise){
          $allCandidatures = $entityManager->getRepository('App\Entity\Candidature')->hasCandidatureForEntreprise($entreprise);
            if (count($allCandidatures) > 0){
              $labelsCandidatures[] = $entreprise->getName();
              $dataCandidatures[] = count($allCandidatures);
            }

        }

        $chartCandidatures = $chartBuilder->createChart(Chart::TYPE_BAR_HORIZONTAL);
        $chartCandidatures->setData([
          'labels' => $labelsCandidatures,
          'datasets' => [
            [
              'label' => 'Candidatures reçues',
              'backgroundColor' => 'rgb(204, 227, 255)',
              'borderColor' => 'rgb(78, 133, 228)',
              'data' => $dataCandidatures,
            ],
          ],
        ]);

        $user = $this->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'entreprises' => $entreprises,
            'blogs' => $blogs,
            'annonces' => $annonces,
            'candidatures' => $candidatures,
            'agendas' => $agendas,
            'chartAnnonces' => $chartAnnonces,
            'chartCandidatures' => $chartCandidatures
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $user= $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('files')->getData()){
                $this->uploadFile($form->get('files')->getData(), $user);
            }
            $user->updateTimestamps();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_MODIFICATION_PROFILE']);

            $loader = new ArrayLoader([
                'email' => $email->getContent(),
            ]);

            $twig = new Environment($loader);
            $message = $twig->render('email',['user' => $user]);

            $mailer->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => $email->getSubject(),
                'html_template'   => 'emails/email_vide.html.twig',
                'context'         => [
                    'message' => $message
                ]
            ]);

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('user_parametres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/{userId}', name: 'app_profile_default_avatar')]
    public function changeAvatar($id, $userId): Response
    {
      $entityManager = $this->getDoctrine()->getManager();
      $image = $entityManager->getRepository(File::class)->find($id);
      $user = $entityManager->getRepository(User::class)->find($userId);

      $user->setAvatar($image);
      $entityManager->persist($user);
      $entityManager->flush();

      return $this->redirectToRoute('app_profile_edit', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pass_modifier', name: 'pass_modifier', methods: ['GET', 'POST'])]
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($request->isMethod('POST')){

            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $oldPassword = $request->request->get('oldPass');
            if (!$passwordEncoder->isPasswordValid($user, $oldPassword)){
                $this->addFlash('warning', 'Mot de passe incorrect');

                return $this->redirectToRoute('pass_modifier');
            }

            // On vérifie si les 2 mots de passe sont identiques
            if($request->request->get('password') == $request->request->get('password2')){
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
                $user->updateTimestamps();
                $em->flush();
                $this->addFlash('success', 'Mise à jour réussie');

                $email = $em->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_RESET_PASSWORD_AFTER_LOGIN']);

                $loader = new ArrayLoader([
                    'email' => $email->getContent(),
                ]);

                $twig = new Environment($loader);
                $message = $twig->render('email',['user' => $user]);

                $this->addFlash('success', 'Candidature réussie');

                $mailer->send([
                    'recipient_email' => $user->getEmail(),
                    'subject'         => $email->getSubject(),
                    'html_template'   => 'emails/email_vide.html.twig',
                    'context'         => [
                        'message' => $message
                    ]
                ]);

                return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->addFlash('error', 'Les deux mots de passe doivent être identiques');
            }
        }

        return $this->render('user/editpassword.html.twig');
    }

    #[Route('/data', name: 'user_data')]
    public function usersData(): Response
    {
        return $this->render('user/data.html.twig');
    }

    #[Route('/data_download', name: 'data_download')]
    public function usersDataDownload(): Response
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
        $html = $this->renderView('user/data_download.html.twig');

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // nom du fichier
        $fichier = 'user-data-'. $this->getUser().'.pdf';

        // Envoi du PDF au navigateur
        $dompdf->stream($fichier, [
            'Attachment' => true
        ]);

        return new Response();
    }

    /**
     * @param $file
     * @param $user
     */
    public function uploadFile($file, $user)
    {
        $image = $file;
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $name = $image->getClientOriginalName();
        $image->move(
            $this->getParameter('files_directory'),
            $fichier
        );
        $img = new File();
        $img->setName($fichier);
        $img->setNameFile($name);
        $img->setType(File::TYPE_AVATAR);
        $user->addFiles($img);
        $user->setAvatar($img);
    }

    #[Route('/supprime/file/{id}', name: 'user_delete_files', methods: ['DELETE'])]
    public function deleteImage(File $file, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

          if($this->isCsrfTokenValid('delete'.$file->getId(), $data['_token'])){
            $nom = $file->getName();
            unlink($this->getParameter('files_directory').'/'.$nom);

            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index');
    }

    #[Route('/parametres', name: 'user_parametres')]
    public function parametres(): Response
    {
        $user = $this->getUser();
        return $this->render('user/parametres.html.twig',[
            'user' => $user
        ]);
    }
}
