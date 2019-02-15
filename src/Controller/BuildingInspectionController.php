<?php


namespace App\Controller;


use App\Entity\BuildingInspection;
use App\Entity\BuildingInspectionItem;
use App\Form\BuildingInspectionType;
use App\Repository\BuildingInspectionItemHeadersRepository;
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
     * @return Response
     */
    public function list(): Response
    {
//        $repository = $this->getDoctrine()->getRepository(Address::class);
//
//        $addresses = $repository->findBy([], ['street' => 'ASC']);

        return $this->render('inspection/list.html.twig', [
//            'addresses' => $addresses
        ]);
    }

    /**
     * @Route(
     *  "/ajouter/",
     *  name="app_inspection_new"
     * )
     *
     * @param Request                                 $request
     * @param BuildingInspectionItemHeadersRepository $headersRepository
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function new(Request $request, BuildingInspectionItemHeadersRepository $headersRepository)
    {
        $inspection = new BuildingInspection();
        $headers = $headersRepository->findAll();
        foreach ($headers as $header) {
            $item = new BuildingInspectionItem();
            $item->setHeaders($header);
            $inspection->addItem($item);
        }

        $form = $this->createForm(BuildingInspectionType::class, $inspection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist to DB
            $em = $this->getDoctrine()->getManager();

            /** @var BuildingInspection $inspection */
            $inspection = $form->getData();
            $items = $inspection->getItems()->getValues();

            foreach ($items as $item) {
                $em->persist($item);
            }
            $em->persist($inspection);
            $em->flush();

            // Set a "flash" success message
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
