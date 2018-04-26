<?php

namespace App\Controller;

use App\Entity\Accomodation;
use App\Form\AccomodationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse; // function access
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logements")
 */
class AccomodationController extends Controller
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
     *  name="app_accomodation_new"
     * )
     */
    public function new(Request $request)
    {
        $accomodation = new Accomodation();

        $form = $this->createForm(AccomodationType::class, $accomodation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist to DB
            $em = $this->getDoctrine()->getManager();

            $accomodation = $form->getData();

            $em->persist($accomodation);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Le logement a bien été ajouté.'
            );

            return $this->redirectToRoute('app_accomodation_list');
        }

        return $this->render('accomodation/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *  "/modifier/{id}",
     *  name="app_accomodation_edit",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function edit(Accomodation $accomodation, Request $request)
    {
        $form = $this->createForm(AccomodationType::class, $accomodation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();

            $accomodation = $form->getData();

            $em->persist($accomodation);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Les modifications ont bien été enregistrées.'
            );

            return $this->redirectToRoute('app_accomodation_list');
        }

        return $this->render('accomodation/edit.html.twig', [
            'form' => $form->createView(),
            'accomodation' => $accomodation
        ]);
    }

    /**
     * @Route(
     *  "/supprimer/{id}",
     *  name="app_accomodation_delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function delete(Accomodation $accomodation)
    {
        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($accomodation);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'Le logement a bien été supprimé.'
        );

        return $this->redirectToRoute('app_accomodation_list');
    }


// ██████╗  █████╗  ██████╗ ███████╗███████╗
// ██╔══██╗██╔══██╗██╔════╝ ██╔════╝██╔════╝
// ██████╔╝███████║██║  ███╗█████╗  ███████╗
// ██╔═══╝ ██╔══██║██║   ██║██╔══╝  ╚════██║
// ██║     ██║  ██║╚██████╔╝███████╗███████║
// ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝


    /**
     * @Route(
     *  "/{page}",
     *  name="app_accomodation_list",
     *  requirements={
     *      "page"="\d+"
     *  }
     * )
     */
    public function list($page = 1)
    {    
        $repository = $this->getDoctrine()->getRepository(Accomodation::class);

        $accomodations = $repository->findBy([], ['street' => 'ASC']);

        return $this->render('accomodation/list.html.twig', [
            'accomodations' => $accomodations
        ]);
    }


//  █████╗      ██╗ █████╗ ██╗  ██╗
// ██╔══██╗     ██║██╔══██╗╚██╗██╔╝
// ███████║     ██║███████║ ╚███╔╝ 
// ██╔══██║██   ██║██╔══██║ ██╔██╗ 
// ██║  ██║╚█████╔╝██║  ██║██╔╝ ██╗
// ╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═╝  ╚═╝

    
    /**
     * @Route(
     *  "/info",
     *  name="app_accomodation_info",
     * )
     */
    public function info(Request $request)
    {
        $id = $request->request->get('id');

        // Retrieve the requested Accomodation
        $accomodation = $this->getDoctrine()
            ->getRepository(Accomodation::class)
            ->find($id);

        // Get values
        $access = $accomodation->getAccess();
        $ownerType = $accomodation->getOwnerType();

        // Format values in JSON
        $json = [
            'access' => $access,
            'ownerType' => $ownerType
        ];

        // Return a JSON response
        return new JsonResponse($json);
    }
}
