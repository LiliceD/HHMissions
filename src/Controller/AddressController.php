<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse; // function access
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AddressController
 *
 * @Route("/logements")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class AddressController extends Controller
{
    
    //  ██████╗██████╗ ██╗   ██╗██████╗
    // ██╔════╝██╔══██╗██║   ██║██╔══██╗
    // ██║     ██████╔╝██║   ██║██║  ██║
    // ██║     ██╔══██╗██║   ██║██║  ██║
    // ╚██████╗██║  ██║╚██████╔╝██████╔╝
    //  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝

    /**
     * @Route(
     *  "/ajouter/",
     *  name="app_address_new"
     * )
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist to DB
            $em = $this->getDoctrine()->getManager();

            $address = $form->getData();

            $em->persist($address);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Le logement a bien été ajouté.'
            );

            return $this->redirectToRoute('app_address_list');
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *  "/{id}/modifier/",
     *  name="app_address_edit",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param Address $address
     * @param Request      $request
     *
     * @return RedirectResponse|Response
     */
    public function edit(Address $address, Request $request)
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();

            $address = $form->getData();

            $em->persist($address);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Les modifications ont bien été enregistrées.'
            );

            return $this->redirectToRoute('app_address_list');
        }

        return $this->render('address/edit.html.twig', [
            'form' => $form->createView(),
            'address' => $address
        ]);
    }

    /**
     * @Route(
     *  "/{id}/supprimer/",
     *  name="app_address_delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     *
     * @param Address $address
     *
     * @return RedirectResponse
     */
    public function delete(Address $address): RedirectResponse
    {
        // Check if address is linked to missions
        $missions = $address->getMissions();

        if (!$missions->isEmpty()) {
            // Set a "flash" success message
            $this->addFlash(
                'error',
                'L\'adresse n\'a pas pu être supprimée car elle est liée à des missions.'
            );
        } else {
            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'Le logement a bien été supprimé.'
            );
        }

        return $this->redirectToRoute('app_address_list');
    }


// ██████╗  █████╗  ██████╗ ███████╗███████╗
// ██╔══██╗██╔══██╗██╔════╝ ██╔════╝██╔════╝
// ██████╔╝███████║██║  ███╗█████╗  ███████╗
// ██╔═══╝ ██╔══██║██║   ██║██╔══╝  ╚════██║
// ██║     ██║  ██║╚██████╔╝███████╗███████║
// ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝


    /**
     * @Route(
     *  "/",
     *  name="app_address_list",
     * )
     *
     * @return Response
     */
    public function list(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Address::class);

        $addresses = $repository->findBy([], ['street' => 'ASC']);

        return $this->render('address/list.html.twig', [
            'addresses' => $addresses
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
     *  "/info/",
     *  name="app_address_info",
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $id = $request->request->get('id');

        // Retrieve the requested Address
        /** @var Address $address */
        $address = $this->getDoctrine()
            ->getRepository(Address::class)
            ->find($id);

        // Get values
        $access = $address->getAccess();
        $ownerType = $address->getOwnerType();
        $glaUser = $address->getGla();
        $gla = [
            'id' => $glaUser->getId(),
            'name' => $glaUser->getName(),
        ];
        $referentUser = $address->getReferent();
        $referent = [
            'id' => 0,
            'name' => '',
        ];

        if ($referentUser instanceof User) {
            $referent = [
                'id' => $referentUser->getId(),
                'name' => $referentUser->getName(),
            ];
        }

        // Format values in JSON
        $json = [
            'access' => $access,
            'ownerType' => $ownerType,
            'gla' => $gla,
            'referent' => $referent,
        ];

        // Return a JSON response
        return new JsonResponse($json);
    }
}
