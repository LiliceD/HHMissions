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
    
    //  ██████╗██████╗ ██╗   ██╗██████╗ 
    // ██╔════╝██╔══██╗██║   ██║██╔══██╗
    // ██║     ██████╔╝██║   ██║██║  ██║
    // ██║     ██╔══██╗██║   ██║██║  ██║
    // ╚██████╗██║  ██║╚██████╔╝██████╔╝
    //  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝ 


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
            // Encode and set initial password to username
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set roles
            $category = $form->get('category')->getData();
            $roles = [$category];

            if ($category === 'ROLE_VOLUNTEER' && $form->get('glaRole')->getData()) {
                // Add 'ROLE_GLA' to volunteers allowed to create missions
                array_push($roles, 'ROLE_GLA');
            }

            $user->setRoles($roles);

            // Persist to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'L\'utilisateur·trice a bien été créé·e.'
            );

            return $this->redirectToRoute('app_mission_list');
        }

        return $this->render(
            'user/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route(
     *  "/voir/{id}",
     *  name="app_user_view",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function view(User $user)
    {
        return $this->render('user/view.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route(
     *  "/modifier/{id}",
     *  name="app_user_edit",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Retrieve app user and allow action only if they are admin or editing themselves
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $appUser = $this->getUser();

        if($this->isGranted('ROLE_ADMIN') || $appUser === $user) {

            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                // Encode and set the password
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                // Set roles
                $category = $form->get('category')->getData();
                $roles = [$category];

                if ($category === 'ROLE_VOLUNTEER' && $form->get('glaRole')->getData()) {
                    // Add 'ROLE_GLA' to volunteers allowed to create missions
                    array_push($roles, 'ROLE_GLA');
                }

                $user->setRoles($roles);

                // Persist changes to DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // Redirect to user view
                return $this->redirectToRoute('app_user_view', [
                    'id' => $user->getId()
                ]);
            }

            return $this->render('user/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user
            ]);
        } else {
            throw $this->createAccessDeniedException('Un·e utilisateur·trice ne peut être modifié·e que par lui-même ou par un·e admin.');
        }
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


//  █████╗      ██╗ █████╗ ██╗  ██╗
// ██╔══██╗     ██║██╔══██╗╚██╗██╔╝
// ███████║     ██║███████║ ╚███╔╝ 
// ██╔══██║██   ██║██╔══██║ ██╔██╗ 
// ██║  ██║╚█████╔╝██║  ██║██╔╝ ██╗
// ╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═╝  ╚═╝
    

    /**
     * Send a text Response with a list of users in $category and whose name matches $search
     *
     * @Route(
     *  "/suggestions",
     *  name="app_user_suggestions"
     * )
     */
    public function searchSuggestions(Request $request)
    {
        // Get request parameters
        $category = $request->request->get('category');
        $search = $request->request->get('search');

        // Get all users of the requested category, ordered alphabetically
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

        // Convert array to text and send response
        $response = new Response();
        $response->setContent(implode('|', $matchingUsers));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
