<?php

namespace App\Controller;

use App\Entity\Accomodation;
use App\Entity\Mission;
use App\Form\MissionType;
use App\Service\FileUploader; // to use FileUploader service in edit
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security; // for @Security annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception; // to throw new Exception()
use Symfony\Component\HttpFoundation\File\File; // for new File() in edit
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/missions")
 */
class MissionController extends Controller
{
    
    //  ██████╗██████╗ ██╗   ██╗██████╗ 
    // ██╔════╝██╔══██╗██║   ██║██╔══██╗
    // ██║     ██████╔╝██║   ██║██║  ██║
    // ██║     ██╔══██╗██║   ██║██║  ██║
    // ╚██████╗██║  ██║╚██████╔╝██████╔╝
    //  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚═════╝ 

    /**
     * @Route(
     *  "/ajouter",
     *  name="app_mission_new"
     * )
     */
    public function new(Request $request, FileUploader $fileUploader)
    {
        $mission = new Mission();

        // Create form
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Manage attachment
            $file = $mission->getAttachment();

            if ($file) {
                $fileName = $fileUploader->upload($file);

                $mission->setAttachment($fileName);
            }

            // Persist to DB
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            // Redirect to mission view
            return $this->redirectToRoute('app_mission_view', [
                'id' => $mission->getId()
            ]);
        }

        return $this->render('mission/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *  "/voir/{id}",
     *  name="app_mission_view",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function view(Mission $mission)
    {
        return $this->render('mission/view.html.twig', [
            'mission' => $mission
        ]);
    }

    /**
     * @Route(
     *  "/modifier/{id}",
     *  name="app_mission_edit",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function edit(Mission $mission, Request $request, FileUploader $fileUploader)
    {
        // Retrieve user and allow action only if user is admin, mission's gla or mission's volunteer
        $this->denyAccessUnlessGranted('simpleEdit', $mission);
        
        // Save previous attachment file name to avoid delete by edit with no changes
        $previousFileName = $mission->getAttachment();

        // Set file from file name
        if ($previousFileName) {
            $mission->setAttachment(
                new File($this->getParameter('app_attachment_directory').'/'.$previousFileName)
            );
        }

        // Create form
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Update status
            $mission->updateStatus();

            // Manage attachment
            $file = $mission->getAttachment();

            if ($file) {
                $fileName = $fileUploader->upload($file);

                $mission->setAttachment($fileName);
            } else if ($previousFileName) {
                $mission->setAttachment($previousFileName);
            }

            // Persist to DB
            $em = $this->getDoctrine()->getManager();

            $mission = $form->getData();

            $em->persist($mission);
            $em->flush();

            // Redirect to mission view
            return $this->redirectToRoute('app_mission_view', [
                'id' => $mission->getId()
            ]);
        }

        return $this->render('mission/edit.html.twig', [
            'form' => $form->createView(),
            'mission' => $mission,
            'attachmentUrl' => $previousFileName
        ]);
    }

    /**
     * @Route(
     *  "/supprimer/{id}",
     *  name="app_mission_delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function delete(Mission $mission, FileUploader $fileUploader)
    {
        // Allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('edit', $mission);
        
        // Delete attached file from server
        $fileName = $mission->getAttachment();
        $fileUploader->delete($fileName);

        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->remove($mission);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'La fiche mission a bien été supprimée.'
        );

        return $this->redirectToRoute('app_mission_list');
    }


// ██████╗  █████╗  ██████╗ ███████╗███████╗
// ██╔══██╗██╔══██╗██╔════╝ ██╔════╝██╔════╝
// ██████╔╝███████║██║  ███╗█████╗  ███████╗
// ██╔═══╝ ██╔══██║██║   ██║██╔══╝  ╚════██║
// ██║     ██║  ██║╚██████╔╝███████╗███████║
// ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝


    /**
     * @Route(
     *  "/{page}",
     *  name="app_mission_list",
     *  requirements={
     *      "page"="\d+"
     *  }
     * )
     */
    public function list($page = 1)
    {    
        $repository = $this->getDoctrine()->getRepository(Mission::class);

        // Missions not assigned yet
        $newMissions = $repository->findByStatus([Mission::STATUS_DEFAULT], 'DESC');
        // Missions assigned but not finished
        $assignedMissions = $repository->findByStatus([Mission::STATUS_ASSIGNED], 'DESC');
        // Missions finished (incl. closed)
        $finishedMissions = $repository->findByStatus([Mission::STATUS_FINISHED, Mission::STATUS_CLOSED], 'DESC');

        if (!$newMissions && !$assignedMissions && !$finishedMissions) {
            throw $this->createNotFoundException('Aucune fiche mission trouvée.');
        }

        return $this->render('mission/list.html.twig', [
            'newMissions' => $newMissions,
            'assignedMissions' => $assignedMissions,
            'finishedMissions' => $finishedMissions,
        ]);
    }

    /**
     * Recap of opened (= any status but "closed") missions
     *
     * @Route(
     *  "/recap/{page}",
     *  name="app_mission_recap",
     *  requirements={
     *      "page"="\d+"
     *  }
     * )
     */
    public function recap($page = 1)
    {    
        $missions = $this->getNonClosedMissions();

        return $this->render('mission/recap.html.twig', [
            'missions' => $missions
        ]);
    }


//  █████╗  ██████╗████████╗██╗ ██████╗ ███╗   ██╗███████╗
// ██╔══██╗██╔════╝╚══██╔══╝██║██╔═══██╗████╗  ██║██╔════╝
// ███████║██║        ██║   ██║██║   ██║██╔██╗ ██║███████╗
// ██╔══██║██║        ██║   ██║██║   ██║██║╚██╗██║╚════██║
// ██║  ██║╚██████╗   ██║   ██║╚██████╔╝██║ ╚████║███████║
// ╚═╝  ╚═╝ ╚═════╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝


    /**
     * Assign user as volunteer of the mission
     * 
     * @Route(
     *  "/assigner/{id}",
     *  name="app_mission_assign",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function assign(Mission $mission)
    {
        // Allow action only if user is a volunteer and mission is not assigned
        $this->denyAccessUnlessGranted('assign', $mission);
        
        // Retrieve user
        $user = $this->getUser();

        // Set volunteer, dateAssigned and status
        $mission->setVolunteer($user);
        $mission->setDateAssigned(new \DateTime());
        $mission->setStatus(Mission::STATUS_ASSIGNED);

        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'La mission vous a bien été attribuée.'
        );

        // Redirect to Mission view
        return $this->redirectToRoute('app_mission_view', [
            'id' => $mission->getId()
        ]);
    }

    /**
     * Close / re-open a mission if its current status is finished / closed respectively
     * 
     * @Route(
     *  "/fermer/{id}",
     *  name="app_mission_close",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function close(Mission $mission)
    {
        // Allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('edit', $mission);

        $status = $mission->getStatus();

        // Change status and set a "flash" success message
        if ($status === Mission::STATUS_FINISHED) {
            $mission->setStatus(Mission::STATUS_CLOSED);

            $this->addFlash(
                'notice',
                'La fiche mission a bien été fermée.'
            );
        } else if ($status === Mission::STATUS_CLOSED) {
            $mission->setStatus(Mission::STATUS_FINISHED);

            $this->addFlash(
                'notice',
                'La fiche mission a bien été ré-ouverte.'
            );
        } else {
            throw new Exception('Une mission doit être terminée pour être fermée.');
        }

        // Persist changes to DB
        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush();

        // Redirect to Mission view
        return $this->redirectToRoute('app_mission_view', [
            'id' => $mission->getId()
        ]);
    }

    /**
     * Empty field "attachment" and delete file
     * 
     * @Route(
     *  "/supprimer-pj/{id}",
     *  name="app_mission_attachment-delete",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function deleteAttachment(Mission $mission, FileUploader $fileUploader)
    { 
        // Allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('edit', $mission);

        // Delete file from server
        $fileName = $mission->getAttachment();
        $fileUploader->delete($fileName);

        // Empty field in database
        $em = $this->getDoctrine()->getManager();
        
        $mission->setAttachment(null);

        $em->persist($mission);
        $em->flush();

        // Set a "flash" success message
        $this->addFlash(
            'notice',
            'La pièce jointe a bien été supprimée.'
        );

        return $this->redirectToRoute('app_mission_view', [
                'id' => $mission->getId()
        ]);
    }

    /**
     * Export a given mission to PDF
     * 
     * @Route(
     *  "/pdf/{id}",
     *  name="app_mission_pdf-export",
     *  requirements={
     *      "id"="\d+"
     *  }
     * )
     */
    public function pdfExport(Mission $mission)
    {
        $html = $this->renderView('pdf/mission-view.html.twig', [
            'mission' => $mission
        ]);

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
     * Export recap of opened missions to PDF
     * 
     * @Route(
     *  "/recap/pdf",
     *  name="app_mission_recap_pdf-export"
     * )
     */
    public function recapPdfExport()
    {
        $missions = $this->getNonClosedMissions();

        $html = $this->renderView('pdf/mission-recap.html.twig', [
            'missions' => $missions
        ]);

        $filename = sprintf('fm-recap_%s.pdf', date('Y-m-d_His'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, [
                'page-size' => 'A3',
                'orientation' => 'Landscape'
            ]),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }


// ███████╗██╗   ██╗███╗   ██╗ ██████╗████████╗██╗ ██████╗ ███╗   ██╗███████╗
// ██╔════╝██║   ██║████╗  ██║██╔════╝╚══██╔══╝██║██╔═══██╗████╗  ██║██╔════╝
// █████╗  ██║   ██║██╔██╗ ██║██║        ██║   ██║██║   ██║██╔██╗ ██║███████╗
// ██╔══╝  ██║   ██║██║╚██╗██║██║        ██║   ██║██║   ██║██║╚██╗██║╚════██║
// ██║     ╚██████╔╝██║ ╚████║╚██████╗   ██║   ██║╚██████╔╝██║ ╚████║███████║
// ╚═╝      ╚═════╝ ╚═╝  ╚═══╝ ╚═════╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚══════╝


    private function getNonClosedMissions()
    {
        $nonClosedStatuses = array_values(Mission::getStatuses()); // Array of possible statuses
        array_pop($nonClosedStatuses); // Remove STATUS_CLOSED

        $repository = $this->getDoctrine()->getRepository(Mission::class);

        // Get all missions with status not "Fermée" ordered by ASC id
        $missions = $repository->findByStatus($nonClosedStatuses, 'ASC');

        return $missions;
    }

}
