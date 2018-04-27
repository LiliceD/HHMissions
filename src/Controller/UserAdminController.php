<?php

namespace App\Controller;

use App\Form\UserType;
use App\Form\UserEditType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UserAdminController extends Controller
{
    
    //  ██████╗██████╗ ██╗   ██╗██████╗ 
    // ██╔════╝██╔══██╗██║   ██║██╔══██╗
    // ██║     ██████╔╝██║   ██║██║  ██║
    // ██║     ██╔══██╗██║   ██║██║  ██║
    // ╚██████╗██║  ██║╚██████╔╝██████╔╝
    //  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝ 


    /**
     * @Route(
     *  "/ajouter",
     *  name="app_user_new"
     * )
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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
            $user->setRolesFromCategory($category);

            // Persist to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'L\'utilisateur·trice a bien été créé·e.'
            );

            return $this->redirectToRoute('app_user_list');
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
        // Create form
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode and set the password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set roles
            $category = $form->get('category')->getData();
            $user->setRolesFromCategory($category);

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
        // Check if user is linked to missions
        $missionsAsGla = $user->getMissionsAsGla();
        $missionsAsVolunteer = $user->getMissionsAsVolunteer();

        if (!$missionsAsGla->isEmpty() || !$missionsAsVolunteer->isEmpty()) {
            // Set a "flash" success message
            $this->addFlash(
                'error',
                'Ce compte n\'a pas pu être supprimé car il est lié à des missions.'
            );

            // Redirect to user view
            return $this->redirectToRoute('app_user_view', [
                'id' => $user->getId()
            ]);
        }

        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'Le compte a bien été supprimé.'
        );

        return $this->redirectToRoute('app_user_list');
    }


// ██████╗  █████╗  ██████╗ ███████╗███████╗
// ██╔══██╗██╔══██╗██╔════╝ ██╔════╝██╔════╝
// ██████╔╝███████║██║  ███╗█████╗  ███████╗
// ██╔═══╝ ██╔══██║██║   ██║██╔══╝  ╚════██║
// ██║     ██║  ██║╚██████╔╝███████╗███████║
// ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝


    /**
     * @Route(
     *  "",
     *  name="app_user_list"
     * )
     */
    public function list()
    {    
        // Get all volunteers / glas / admins ordered alphabetically
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy([], ['id' => 'DESC']);

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }


//  █████╗  ██████╗████████╗██╗ ██████╗ ███╗   ██╗███████╗
// ██╔══██╗██╔════╝╚══██╔══╝██║██╔═══██╗████╗  ██║██╔════╝
// ███████║██║        ██║   ██║██║   ██║██╔██╗ ██║███████╗
// ██╔══██║██║        ██║   ██║██║   ██║██║╚██╗██║╚════██║
// ██║  ██║╚██████╗   ██║   ██║╚██████╔╝██║ ╚████║███████║
// ╚═╝  ╚═╝ ╚═════╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝


    /**
     * (De)activate user
     * 
     * @Route(
     *  "/desactiver/{id}",
     *  name="app_user_deactivate",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function changeActive(User $user)
    {
        // Retrieve app user to prevent self-deactivation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $appUserId = $this->getUser()->getId();

        if ($user->getId() === $appUserId) {
            // Set a "flash" success message
            $this->addFlash(
                'error',
                'Oups ! Vous ne pouvez pas vous désactiver vous-même 😉'
            );
        } else {
            $user->setActive(!$user->isActive());

            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Ce compte a été '.($user->isActive() ? '' : 'dés').'activé.'
            );
        }

        // Redirect to user view
        return $this->redirectToRoute('app_user_view', [
            'id' => $user->getId()
        ]);
    }
}
