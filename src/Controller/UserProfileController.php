<?php

namespace App\Controller;

use App\Form\UserPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserProfileController
 *
 * @Route("/monprofil")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserProfileController extends Controller
{
    /**
     * @Route(
     *  "/",
     *  name="app_profile_view",
     * )
     *
     * @return Response
     */
    public function view(): Response
    {
        // Retrieve app user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/view.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route(
     *  "/changermdp/",
     *  name="app_profile_change-pwd",
     * )
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return RedirectResponse|Response
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Retrieve app user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Create form
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode and set the password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Votre mot de passe a bien été enregistré.'
            );

            // Redirect to user view
            return $this->redirectToRoute('app_profile_view');
        }

        return $this->render('user/change-pwd.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
