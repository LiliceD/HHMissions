<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\BuildingInspectionType;
use App\Manager\AddressManager;
use App\Manager\BuildingInspectionManager;
use Knp\Snappy\GeneratorInterface;
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
     *  "",
     *  name="app_inspection_list",
     * )
     *
     * @param AddressManager $addressManager
     *
     * @return Response
     */
    public function list(AddressManager $addressManager): Response
    {
        return $this->render('inspection/list.html.twig', [
            'addresses' => $addressManager->getBuildings(),
            'inspection' => true,
        ]);
    }

    /**
     * @Route(
     *  "/ajouter",
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

    /**
     * @Route(
     *  "/{id}/voir",
     *  name="app_inspection_view",
     *  requirements={"id"="\d+"}
     * )
     *
     * @param int                       $id
     * @param BuildingInspectionManager $manager
     *
     * @return Response
     */
    public function view(int $id, BuildingInspectionManager $manager): Response
    {
        return $this->render('inspection/view.html.twig', [
            'inspection' => $manager->get($id),
            'headers' => $manager->getItemHeaders(),
        ]);
    }

    /**
     * @Route(
     *  "/recap/{id}",
     *  name="app_inspection_recap",
     *  requirements={"id"="\d+"}
     * )
     *
     * @param Address                   $address
     * @param BuildingInspectionManager $manager
     *
     * @return Response
     */
    public function recap(Address $address, BuildingInspectionManager $manager): Response
    {
        $headers = $manager->getItemHeaders();
        $inspections = $manager->getByAddress($address);

        return $this->render('inspection/recap.html.twig', [
            'address' => $address,
            'headers' => $headers,
            'inspections' => $inspections,
        ]);
    }

    /**
     * @Route(
     *     "/pdf",
     *     name="app_inspection_pdf"
     * )
     *
     * @param BuildingInspectionManager $manager
     * @param GeneratorInterface        $knpSnappy
     *
     * @return Response
     */
    public function downloadPdf(BuildingInspectionManager $manager, GeneratorInterface $knpSnappy): Response
    {
        $headers = $manager->getItemHeaders();
        $html = $this->renderView('pdf/building-inspection-empty.html.twig', [
            'headers' => $headers,
        ]);
        $filename = 'RVI_modele.pdf';

        return new Response(
            $knpSnappy->getOutputFromHtml($html, [
                'page-size' => 'A4',
                'orientation' => 'Landscape',
            ]),
            Response::HTTP_OK,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}
