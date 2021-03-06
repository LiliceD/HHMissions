<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Manager\MailManager;
use SendGrid\Mail\TypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @Route(
     *     "/login/",
     *     name="login"
     * )
     *
     * @param AuthenticationUtils           $authUtils
     * @param AuthorizationCheckerInterface $authChecker
     *
     * @return RedirectResponse|Response
     */
    public function login(AuthenticationUtils $authUtils, AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // If user is connected, redirect to mission list
            return $this->redirectToRoute('app_mission_list');
        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route(
     *     "/reinitialisermdp/",
     *     name="app.reset-pwd"
     * )
     *
     * @param Request                       $request
     * @param AuthorizationCheckerInterface $authChecker
     * @param MailManager                   $mailManager
     * @param UserPasswordEncoderInterface  $passwordEncoder
     *
     * @return RedirectResponse|Response
     *
     * @throws TypeException
     * @throws \Exception
     */
    public function resetPassword(Request $request, AuthorizationCheckerInterface $authChecker, MailManager $mailManager, UserPasswordEncoderInterface $passwordEncoder)
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
            /** @var User $user */
            $user = $repository->findOneBy(['username' => $username]);

            if ($user) {
                // Reset and encode password
                $password = bin2hex(random_bytes(10));
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);

                // Send email
                $mailParams = [
                    'subject' => 'Alors, on est tête en l\'air ? 😉',
                    'to' => $user->getEmail(),
                ];

                $view = $this->renderView('emails/reset-pwd.html.twig', [
                    'name' => $user->getName(),
                    'password' => $password,
                ]);

                $mailManager->send($mailParams['subject'], $mailParams['to'], $view);

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
}
