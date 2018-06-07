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
     *
     * @throws \Exception
     */
    public function resetPassword(Request $request, AuthorizationCheckerInterface $authChecker, MailController $mailController, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If user is connected, redirect to mission list
            return $this->redirectToRoute('app_mission_list');
        }

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
                $newPassword = bin2hex(random_bytes(10));

                $password = $passwordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($password);

                // Set mail parameters
                $mailParams = [
                    'subject' => 'Alors, on est tête en l\'air ? 😉',
                    'to' => [
                        'email' => $user->getEmail(),
                        'name' => $user->getName(),
                    ],
                ];

                // Generate mail HTML body
                $view = $this->renderView('emails/reset-pwd.html.twig', [
                    'name' => $mailParams['to']['name'],
                    'password' => $newPassword,
                ]);

                // Send mail
                $mailController->send($mailParams['subject'], $mailParams['to'], $view);

                // Persist changes to DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

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

    /**
     * @Route("/reinitialisermdp2", name="app.reset-pwd2")
     */
    public function resetPassword2(Request $request, AuthorizationCheckerInterface $authChecker, MailController $mailController, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If user is connected, redirect to mission list
            // return $this->redirectToRoute('app_mission_list');
        }

        // Create form
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        $mailController->validate();

        return $this->render('security/reset-pwd.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
