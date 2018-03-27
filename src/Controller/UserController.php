<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/ajouter", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Create User and form
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Encode the password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set isGla and isVolunteer based on category
            $user->setIsGlaIsVolunteer();

            // Save the User
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'L\'utilisateur a bien été créé.'
            );

            return $this->redirectToRoute('app_missions_list');
        }

        return $this->render(
            'user/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route(
     *  "/supprimer/{id}",
     *  name="user_delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function delete(User $user)
    {
        if (!$user) {
            throw $this->createNotFoundException('L\'utilisateur demandé n\'existe pas.');
        }

        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'L\'utilisateur a bien été supprimé.'
        );

        return $this->redirectToRoute('app_missions_list');
    }

}
