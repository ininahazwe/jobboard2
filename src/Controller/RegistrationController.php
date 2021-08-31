<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/new/register", name="app_register", methods={"GET", "POST"})
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        SecurityAuthenticator $authenticator,
        Mailer $mailer
    ): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setModeration('0');

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            $email = $entityManager->getRepository('App:Email')->findOneBy(['code' => 'EMAIL_REGISTRATION']);

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

            $this->addFlash('success', "Un mail de bienvenue vous a été envoyé !");

            //return $this->redirectToRoute('profile_new');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
