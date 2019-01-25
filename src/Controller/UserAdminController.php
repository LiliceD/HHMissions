<?php

namespace App\Controller;

use App\Form\UserType;
use App\Form\UserEditType;
use App\Entity\User;
use App\Manager\UserManager;
use App\Utils\Constant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserAdminController
 *
 * @Route("/admin/users")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
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
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserManager                  $userManager
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserManager $userManager)
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
            $userManager->setRolesFromCategory($user, $category);

            $user->setActivities([Constant::ACTIVITY_GLA]);

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
     *  "/{id}/voir/",
     *  name="app_user_view",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param User $user
     *
     * @return Response
     */
    public function view(User $user): Response
    {
        return $this->render('user/view.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route(
     *  "/{id}/modifier/",
     *  name="app_user_edit",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserManager                  $userManager
     *
     * @return RedirectResponse|Response
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, UserManager $userManager)
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
            $userManager->setRolesFromCategory($user, $category);

            // An GLA belongs to all activities
            if ($user->getCategory() === User::CATEGORY_GLA) {
                $user->setActivities(Constant::getActivities());
            }

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
     *  "/{id}/supprimer/",
     *  name="app_user_delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function delete(User $user): RedirectResponse
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
     *
     * @return Response
     */
    public function list(): Response
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
     *  "/{id}/desactiver/",
     *  name="app_user_deactivate",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param User $user
     *
     * @return RedirectResponse
     */
    public function changeActive(User $user): RedirectResponse
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
