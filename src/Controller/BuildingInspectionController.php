<?php

namespace App\Controller;

use App\Form\BuildingInspectionType;
use App\Manager\AddressManager;
use App\Manager\BuildingInspectionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BuildingInspectionController
 *
 * @Route("/visites")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionController extends AbstractController
{
    /**
     * @Route(
     *  "/",
     *  name="app_inspection_list",
     * )
     *
     * @param AddressManager $addressManager
     *
     * @return Response
     */
    public function list(AddressManager $addressManager): Response
    {
        $addresses = $addressManager->getBuildings();

        return $this->render('inspection/list.html.twig', [
            'addresses' => $addresses
        ]);
    }

    /**
     * @Route(
     *  "/ajouter/",
     *  name="app_inspection_new"
     * )
     *
     * @param Request                   $request
     * @param BuildingInspectionManager $manager
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function new(Request $request, BuildingInspectionManager $manager)
    {
        $inspection = $manager->getNew();

        $form = $this->createForm(BuildingInspectionType::class, $inspection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($inspection);

            $this->addFlash(
                'notice',
                'Le rapport de visite a bien été créé.'
            );

            return $this->redirectToRoute('app_inspection_list');
        }

        return $this->render('inspection/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
