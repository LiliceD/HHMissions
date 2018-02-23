<?php

namespace App\Controller;

// use App\Entity\Accomodation;
use App\Entity\Accomodation;
use App\Form\AccomodationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AccomodationController extends Controller
{
	/**
	* @Route(
	* 	"/logement/add",
	*	name="app_accomodation_add"
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

			return $this->redirectToRoute('app_missions_list');
		}

		return $this->render('accomodation/add.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	* @Route(
	* 	"/logement/edit/{id}",
	*	name="app_accomodation_edit",
	*	requirements={
	*		"id"="\d+"
	*	}
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

			return $this->redirectToRoute('app_missions_list');
		}

		return $this->render('accomodation/edit.html.twig', [
			'form' => $form->createView()
		]);
	}
}