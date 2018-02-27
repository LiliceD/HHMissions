<?php

namespace App\Controller;

use App\Entity\Accomodation;
use App\Entity\Mission;
use App\Form\MissionType;
use App\Service\FileUploader; // to use FileUploader service in edit
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File; // for new File() in edit
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

        return $this->render('missions/list.html.twig', array(
            'missions' => $missions
        ));
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

        return $this->render('missions/view.html.twig', array(
            'mission' => $mission,
            'accomodation' => $accomodation
        ));
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
    public function edit(Mission $mission, Request $request, FileUploader $fileUploader)
    {
        // Saving previous scan to avoid delete by edit
        $previousFileName = $mission->getPdfScan();

        if ($previousFileName) {
            $mission->setPdfScan(
                new File($this->getParameter('pdf_scans_directory').'/'.$previousFileName)
            );
        }

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $mission->getPdfScan();

            if ($file) {
                $fileName = $fileUploader->upload($file);

                $mission->setPdfScan($fileName);
            }
            else if ($previousFileName) {
                $mission->setPdfScan($previousFileName);
            }

            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
            ));
        }

        return $this->render('missions/edit.html.twig', array(
            'form' => $form->createView(),
            'mission' => $mission,
            'pdfScanUrl' => $previousFileName
        ));
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

            return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
            ));
        }

        return $this->render('missions/add.html.twig', array(
            'form' => $form->createView()
        ));
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
    public function delete(Mission $mission, FileUploader $fileUploader)
    {
        // Delete pdf scan from server
        $fileName = $mission->getPdfScan();
        $fileUploader->delete($fileName);

        $em = $this->getDoctrine()->getManager();
        $em->remove($mission);
        $em->flush();

        return $this->redirectToRoute('app_missions_list');
    }

    /**
    * @Route(
    *  "/delete-pdf-scan/{id}",
    *  name="app_missions_pdf-scan-delete",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function deletePdfScan(Mission $mission, FileUploader $fileUploader)
    { 
        // Delete file from server
        $fileName = $mission->getPdfScan();

        $fileUploader->delete($fileName);

        // Empty field in database
        $mission->setPdfScan(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
        ));
    }
}