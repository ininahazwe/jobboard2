<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cms/utilisateurs')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile_index', methods: ['GET'])]
    public function index(ProfileRepository $profileRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('profile/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/candidats', name: 'candidats', methods: ['GET'])]
    public function candidats(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('user/candidats.html.twig', [
            'users' => $userRepository->getAllcandidats(),
        ]);
    }

    #[Route('/recruteurs', name: 'recruteurs', methods: ['GET'])]
    public function recruteurs(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('user/recruteurs.html.twig', [
            'users' => $userRepository->getAllRecruteurs(),
        ]);
    }

    #[Route('/super-recruteurs', name: 'super-recruteurs', methods: ['GET'])]
    public function superRecruteurs(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('user/super_recruteurs.html.twig', [
            'users' => $userRepository->getAllSuperRecruteurs(),
        ]);
    }

    #[Route('/users-en-attente', name: 'user-attente', methods: ['GET'])]
    public function usersEnAttente(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('user/super_recruteurs.html.twig', [
            'users' => $userRepository->getUserEnAttente(),
        ]);
    }

    #[Route('/new', name: 'profile_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $entityManager = $this->getDoctrine()->getManager();

        $profile = new Profile();

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('cv')->getData()){
              $this->uploadFile($form->get('cv')->getData(), $profile, $this->getUser(), $this->getParameter('files_directory'));
            }
            $profile->setUser($this->getUser());
            $profile->getUser()->setModeration('1');

            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Ajout réussi');

            return $this->redirectToRoute('user_parametres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'profile_show', methods: ['GET'])]
    public function show(Profile $profile): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/{id}/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profile $profile): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('cv')->getData()){
              $this->uploadFile($form->get('cv')->getData(), $profile, $this->getUser(), $this->getParameter('files_directory'));
            }

            $profile->updateTimestamps();

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'profile_delete', methods: ['POST'])]
    public function delete(Request $request, Profile $profile): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index');
    }

  /**
   * @param Request $request
   * @return RedirectResponse
   */
  #[Route('/success/login', name: 'success_login')]
    public function successLogin(Request $request): RedirectResponse
  {
        $new = $request->get('new', false);
        if ($this->getUser()){
            if ($new){
                return $this->redirectToRoute('profile_new',['id' => $this->getUser()->getId()]);
            }
            return $this->redirectToRoute('app_profile');
        }
        return $this->redirectToRoute('access_register');
    }

    /**
     * @param $file
     * @param $profile
     */
    public function uploadFile($file, $profile, $user, $parameter)
    {
        $image = $file;
        $fichier = md5(uniqid()) . '.' . $image->guessExtension();
        $name = $image->getClientOriginalName();
        $image->move(
          $parameter,
          $fichier
        );
        $img = new File();
        $img->setName($fichier);
        $img->setNameFile($name);
        $img->setType(File::TYPE_CV);
        $img->setUser($user);
        $profile->addCv($img);
    }

    #[Route('/supprime/file/{id}', name: 'blog_delete_files', methods: ['DELETE'])]
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
}
