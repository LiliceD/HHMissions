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
        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'L\'utilisateur·trice a bien été supprimé·e.'
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
        $users = $repository->findBy([], ['roles' => 'DESC']);

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }
}
