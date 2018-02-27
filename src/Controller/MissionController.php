<?php

namespace App\Controller;

use App\Entity\Accomodation;
use App\Entity\Mission;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/missions")
 */
class MissionController extends Controller
{
    /**
    * @Route(
    *  "/{page}",
    *  name="app_missions_list",
    *  requirements={
    *      "page"="\d+"
    *  }
    * )
    */
    public function list($page = 1)
    {    
        // $em = $this->getDoctrine()->getManager();

  //       $accomodation = new Accomodation();
  //       $accomodation->setAddress('7-9 avenue Saint Romain');

  //       $em->persist($accomodation);

  //       $em->flush();

        $repository = $this->getDoctrine()->getRepository(Mission::class);

        $missions = $repository->findBy(array(), array('dateCreated' => 'DESC'));

        return $this->render('missions/list.html.twig', [
            'missions' => $missions
        ]);
    }

    /**
    * @Route(
    *  "/view/{id}",
    *  name="app_missions_view",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function view(Mission $mission)
    {
        $accomodation = $mission->getAccomodation();

        return $this->render('missions/view.html.twig', [
            'mission' => $mission,
            'accomodation' => $accomodation
        ]);
    }

    /**
    * @Route(
    *  "/edit/{id}",
    *  name="app_missions_edit",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function edit(Mission $mission, Request $request)
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            return $this->redirectToRoute('app_missions_view', [
                'id' => $mission->getId()
            ]);
        }

        return $this->render('missions/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route(
    *  "/add",
    *  name="app_missions_add"
    * )
    */
    public function add(Request $request)
    {
        $mission = new Mission();

        // Set a default accomodation to $mission
        $accomodation = $this->getDoctrine()
            ->getRepository(Accomodation::class)
            ->find(1);
        $mission->setAccomodation($accomodation);

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            return $this->redirectToRoute('app_missions_view', [
                'id' => $mission->getId()
            ]);
        }

        return $this->render('missions/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route(
    *  "/delete/{id}",
    *  name="app_missions_delete",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function delete(Mission $mission)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($mission);
        $em->flush();

        return $this->redirectToRoute('app_missions_list');
    }
}