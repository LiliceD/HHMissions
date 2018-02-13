<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MissionController extends Controller
{
	/**
	* @Route(
	* 	"/",
	*	name="app_missions_index",
	* )
	*/
	public function index()
	{
		return $this->redirectToRoute('app_missions_list');
	}

	/**
	* @Route(
	* 	"/missions/{page}",
	*	name="app_missions_list",
	*	requirements={
	*		"page"="\d+"
	*	}
	* )
	*/
	public function list($page = 1)
	{	
		// $em = $this->getDoctrine()->getManager();

  //       $mission = new Mission();
  //       $mission->setAddress('324 rue de la Vieille 69001 LYON');
  //       $mission->setGla('R.BENSAID');
  //       $mission->setDescription('Visite d\'immeuble');

  //       $em->persist($mission);

  //       $em->flush();

        $repository = $this->getDoctrine()->getRepository(Mission::class);

        $missions = $repository->findAll();
  //       $missionsList = array(
		// 	array('id' => 2, 'gla' => 'C.ROBAYE', 'dateCreated' => 1517828013),
		// 	array('id' => 8, 'gla' => 'R.BENSAID', 'dateCreated' => 1517914413),
		// 	array('id' => 9, 'gla' => 'R.BENSAID', 'dateCreated' => 1518346413)
		// );

		return $this->render('missions/list.html.twig', [
			'missions' => $missions
		]);
	}

	/**
	* @Route(
	* 	"/missions/view/{id}",
	*	name="app_missions_view",
	*	requirements={
	*		"id"="\d+"
	*	}
	* )
	*/
	public function view(Mission $mission)
	{
		return $this->render('missions/view.html.twig', [
			'mission' => $mission
		]);
	}

	/**
	* @Route(
	* 	"/missions/edit/{id}",
	*	name="app_missions_edit",
	*	requirements={
	*		"id"="\d+"
	*	}
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
	* 	"/missions/add",
	*	name="app_missions_add"
	* )
	*/
	public function add(Request $request)
	{
		$mission = new Mission();

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
	* 	"/missions/delete/{id}",
	*	name="app_missions_delete",
	*	requirements={
	*		"id"="\d+"
	*	}
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