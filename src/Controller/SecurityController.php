<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Controller\MailController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If user is connected, redirect to mission list
            return $this->redirectToRoute('app_mission_list');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/reinitialisermdp", name="app.reset-pwd")
     */
    public function resetPassword(Request $request, MailController $mailController, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Create form
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get username
            $data = $form->getData();
            $username = $data['username'];

            // Retrieve user
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(['username' => $username]);

            if ($user) {
                // Reset and encode password
                $password = $passwordEncoder->encodePassword($user, $user->getUsername());
                $user->setPassword($password);

                // Persist changes to DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // Send email
                $mailParams = [
                    'subject' => 'Alors, on est tête en l\'air ? 😉',
                    'to' => $user->getEmail()
                ];

                $viewParams = [
                    'route' => 'emails/reset-pwd.html.twig',
                    'params' => ['name' => $user->getName()]
                ];

                $view = $this->renderView($viewParams['route'], $viewParams['params']);

                $mailController->send($mailParams['subject'], $mailParams['to'], $view);

                // Set a "flash" success message
                $this->addFlash(
                    'notice',
                    'Un email vous a été envoyé à votre adresse Habitat et Humanisme.'
                );

                // Redirect to login
                return $this->redirectToRoute('login');
            }

            // Set a "flash" error message
            $this->addFlash(
                'error',
                'Cet identifiant ne correspond à aucun compte actif.'
            );

            // Stay on same page
            return $this->redirectToRoute('app.reset-pwd');
        }

        return $this->render('security/reset-pwd.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
