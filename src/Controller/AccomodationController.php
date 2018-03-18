<?php

namespace App\Controller;

// use App\Entity\Accomodation;
use App\Entity\Accomodation;
use App\Form\AccomodationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse; // function access
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AccomodationController extends Controller
{
    /**
    * @Route(
    *  "/logements/{page}",
    *  name="app_accomodation_list",
    *  requirements={
    *      "page"="\d+"
    *  }
    * )
    */
    public function list($page = 1)
    {    
        $repository = $this->getDoctrine()->getRepository(Accomodation::class);

        $accomodations = $repository->findBy(array(), array('street' => 'ASC'));

        return $this->render('accomodation/list.html.twig', [
            'accomodations' => $accomodations
        ]);
    }

    /**
    * @Route(
    *  "/logements/add",
    *  name="app_accomodation_add"
    * )
    */
    public function add(Request $request)
    {
        $accomodation = new Accomodation();

        $form = $this->createForm(AccomodationType::class, $accomodation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $accomodation = $form->getData();

            $em->persist($accomodation);
            $em->flush();

            $this->addFlash(
                'notice',
                'Le logement a bien été ajouté à la liste.'
            );

            return $this->redirectToRoute('app_accomodation_list');
        }

        return $this->render('accomodation/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route(
    *  "/logements/edit/{id}",
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
            $em = $this->getDoctrine()->getManager();

            $accomodation = $form->getData();

            $em->persist($accomodation);
            $em->flush();

            $this->addFlash(
                'notice',
                'Les modifications ont bien été enregistrées.'
            );

            return $this->redirectToRoute('app_accomodation_list');
        }

        return $this->render('accomodation/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route(
    *  "/logements/delete/{id}",
    *  name="app_accomodation_delete",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function delete(Accomodation $accomodation)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($accomodation);
        $em->flush();

        $this->addFlash(
            'notice',
            'Le logement a bien été supprimé.'
        );

        return $this->redirectToRoute('app_accomodation_list');
    }

    /**
    * @Route(
    *  "/logements/details",
    *  name="app_accomodation_details",
    * )
    */
    public function details(Request $request)
    {
        $id = $request->query->get('id');

        $accomodation = $this->getDoctrine()
            ->getRepository(Accomodation::class)
            ->find($id);

        $access = $accomodation->getAccess();
        $ownerType = $accomodation->getOwnerType();

        $json = array(
            'access' => $access,
            'ownerType' => $ownerType
        );

        return new JsonResponse($json);
    }
}