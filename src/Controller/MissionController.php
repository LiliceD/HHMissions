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
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

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

        if (!$missions) {
            throw $this->createNotFoundException('Aucune fiche mission trouvée.');

        }

        return $this->render('mission/list.html.twig', array(
            'missions' => $missions
        ));
    }

    /**
    * CREATE
    *
    * @Route(
    *  "/add",
    *  name="app_missions_add"
    * )
    */
    public function add(Request $request, FileUploader $fileUploader)
    {
        $mission = new Mission();

        // Set a default accomodation to $mission
        $accomodation = $this->getDoctrine()
            ->getRepository(Accomodation::class)
            ->findFirst();
        $mission->setAccomodation($accomodation);

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            /**************** Manage attachment ****************/
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $mission->getAttachment();

            if ($file) {
                $fileName = $fileUploader->upload($file);

                $mission->setAttachment($fileName);
            }

            /****************** Persist to DB ******************/
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
            ));
        }

        return $this->render('mission/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
    * READ
    *
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
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');

        }

        return $this->render('mission/view.html.twig', array(
            'mission' => $mission
        ));
    }

    /**
    * UPDATE
    *
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
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');

        }

        // Saving previous scan to avoid delete by edit
        $previousFileName = $mission->getAttachment();

        if ($previousFileName) {
            $mission->setAttachment(
                new File($this->getParameter('app_attachment_directory').'/'.$previousFileName)
            );
        }

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            /****************** Update status ******************/
            // $status = $mission->getStatus(); // Current mission status

            // if ($mission->getDateFinished() !== null && $status !== Mission::STATUS_FINISHED) {
            //     $mission->setStatus(Mission::STATUS_FINISHED);
            // }
            // else if ($mission->getDateAssigned() !== null && $status !== Mission::STATUS_ASSIGNED) {
            //     $mission->setStatus(Mission::STATUS_ASSIGNED);
            // }
            // else if ($status !== Mission::STATUS_DEFAULT) {
            //     $mission->setStatus(Mission::STATUS_DEFAULT);  // Shouldn't happen
            // }
            $mission->updateStatus();

            /**************** Manage attachment ****************/
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $mission->getAttachment();

            if ($file) {
                $fileName = $fileUploader->upload($file);

                $mission->setAttachment($fileName);
            }
            else if ($previousFileName) {
                $mission->setAttachment($previousFileName);
            }

            /****************** Persist to DB ******************/
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
            ));
        }

        return $this->render('mission/edit.html.twig', array(
            'form' => $form->createView(),
            'mission' => $mission,
            'attachmentUrl' => $previousFileName
        ));
    }

    /**
    * DELETE
    * 
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
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');

        }

        // Delete attached file from server
        $fileName = $mission->getAttachment();
        $fileUploader->delete($fileName);

        $em = $this->getDoctrine()->getManager();
        $em->remove($mission);
        $em->flush();

        $this->addFlash(
            'notice',
            'La fiche mission a bien été supprimée.'
        );

        return $this->redirectToRoute('app_missions_list');
    }

    /**
     * Close / re-open a mission if its current status is finished / closed respectively
     * 
     * @Route(
     *  "/close/{id}",
     *  name="app_missions_close",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function close(Mission $mission)
    {
            
        /****************** Update status ******************/
        $status = $mission->getStatus();

        if ($status === Mission::STATUS_FINISHED) {
            $mission->setStatus(Mission::STATUS_CLOSED);

            $this->addFlash(
                'notice',
                'La fiche mission a bien été fermée.'
            );
        }
        else if ($status === Mission::STATUS_CLOSED) {
            $mission->setStatus(Mission::STATUS_FINISHED);

            $this->addFlash(
                'notice',
                'La fiche mission a bien été ré-ouverte.'
            );
        }
        else {
            throw new \Error('Une mission doit être terminée pour être fermée.');
        }

        /****************** Persist to DB ******************/
        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        return $this->redirectToRoute('app_missions_view', array(
            'id' => $mission->getId()
        ));
    }


        /**
    * Empty field "attachment" and delete file
    * 
    * @Route(
    *  "/delete-attachment/{id}",
    *  name="app_missions_attachment-delete",
    *  requirements={
    *      "id"="\d+"
    *  }
    * )
    */
    public function deleteAttachment(Mission $mission, FileUploader $fileUploader)
    { 
        // Delete file from server
        $fileName = $mission->getAttachment();
        $fileUploader->delete($fileName);

        // Empty field in database
        $mission->setAttachment(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        return $this->redirectToRoute('app_missions_view', array(
                'id' => $mission->getId()
        ));
    }

    /**
     * Export a given mission to PDF
     * 
     * @Route(
     *  "/pdf/{id}",
     *  name="app_missions_pdf-export",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function pdfExport(Mission $mission)
    {
        $html = $this->renderView('pdf/mission.html.twig', array(
            'mission' => $mission
        ));

        $filename = sprintf('fm-%s_%s.pdf', $mission->getId(), date('Y-m-d_His'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }


    /**
    * Recap of opened (= any status but "closed") missions
    *
    * @Route(
    *  "/recap/{page}",
    *  name="app_missions_recap",
    *  requirements={
    *      "page"="\d+"
    *  }
    * )
    */
    public function recap($page = 1)
    {    
        $missions = $this->getNonClosedMissions();

        return $this->render('mission/recap.html.twig', array(
            'missions' => $missions
        ));
    }


    /**
     * Export recap of opened missions to PDF
     * 
     * @Route(
     *  "/recap/pdf",
     *  name="app_missions_recap_pdf-export"
     * )
     */
    public function recapPdfExport()
    {
        $missions = $this->getNonClosedMissions();

        $html = $this->renderView('pdf/missions-recap.html.twig', array(
            'missions' => $missions
        ));

        $filename = sprintf('fm-recap_%s.pdf', date('Y-m-d_His'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array(
                'page-size' => 'A3',
                'orientation' => 'Landscape'
            )),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    private function getNonClosedMissions()
    {
        $statuses = array_values(Mission::getStatuses()); // Array of possible statuses
        array_pop($statuses); // Remove STATUS_CLOSED

        $repository = $this->getDoctrine()->getRepository(Mission::class);

        // Get all missions with status not "Fermée"
        $missions = $repository->findByStatus($statuses);

        return $missions;
    }

}