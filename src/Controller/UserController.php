<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/ajouter", name="app_user_registration")
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

            // Set roles
            $category = $form->get('category')->getData();
            $roles = [$category];
            $user->setRoles($roles);

            // Save the User
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'L\'utilisateur a bien été créé.'
            );

            return $this->redirectToRoute('app_mission_list');
        }

        return $this->render(
            'user/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route(
     *  "/supprimer/{id}",
     *  name="app_user_delete",
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

        return $this->redirectToRoute('app_mission_list');
    }

    /**
     * @Route(
     *  "/suggestions",
     *  name="app_user_suggestions"
     * )
     */
    public function userAutocomplete(Request $request)
    {
        $category = $request->request->get('category');
        $search = $request->request->get('search');

        // Get all users with isVolunteer/Gla = true ordered by ASC name
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findByCategory($category);
        
        $matchingUsers = [];

        if ($search) {
            // Fill an array with volunteers/GLAs matching search
            foreach($users as $user) {
                $userName = $user['name'];
                if (stripos($userName, $search) > -1) {
                    array_push($matchingUsers, $userName);
                }
            }
        } else {
            // Or with all volunteers/GLAs if no search input
            foreach($users as $user) {
                $userName = $user['name'];
                array_push($matchingUsers, $userName);
            }
        }

        sort($matchingUsers, SORT_NATURAL);

        $response = new Response();
        $response->setContent(implode('|', $matchingUsers));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
