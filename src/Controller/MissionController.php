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

        // Set a default accomodation to $mission (error otherwise)
        $accomodation = $this->getDoctrine()
            ->getRepository(Accomodation::class)
            ->findFirst();
        $mission->setAccomodation($accomodation);

        // Create form
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Manage attachment
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
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
            return $this->redirectToRoute('app_mission_view', array(
                'id' => $mission->getId()
            ));
        }

        return $this->render('mission/new.html.twig', array(
            'form' => $form->createView()
        ));
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
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');
        }

        return $this->render('mission/view.html.twig', array(
            'mission' => $mission
        ));
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
        // Throw error if mission is not found
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');
        }

        // Closed mission can't be edited
        if ($mission->getStatus() !== Mission::STATUS_CLOSED) {
            
            // Retrieve user and allow action only if user is admin, mission's gla or mission's volunteer
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();

            if($this->isGranted('ROLE_ADMIN') || 
                ($this->isGranted('ROLE_GLA') && $mission->getGla() === $user->getName()) || 
                ($this->isGranted('ROLE_VOLUNTEER') && $mission->getVolunteer() === $user->getName())
            ) {
                // Saving previous scan to avoid delete by edit with no changes
                $previousFileName = $mission->getAttachment();

                if ($previousFileName) {
                    $mission->setAttachment(
                        new File($this->getParameter('app_attachment_directory').'/'.$previousFileName)
                    );
                }

                $form = $this->createForm(MissionType::class, $mission);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    
                    // Update status
                    $mission->updateStatus();

                    // Manage attachment
                    /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
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
                    return $this->redirectToRoute('app_mission_view', array(
                        'id' => $mission->getId()
                    ));
                }

                return $this->render('mission/edit.html.twig', array(
                    'form' => $form->createView(),
                    'mission' => $mission,
                    'attachmentUrl' => $previousFileName
                ));
            } else {
                throw $this->createAccessDeniedException('Une fiche mission ne peut être modifiée que par la/le GLA qui l\'a créée, par la/le bénévole qui l\'a prise en charge ou par un·e admin.');
            }
        } else {
            throw new Exception('Une mission fermée ne peut plus être modifiée.');
        }
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
        if (!$mission) {
            throw $this->createNotFoundException('La fiche mission demandée n\'existe pas.');
        }

        // Retrieve user and allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('ROLE_GLA');
        $user = $this->getUser();

        if($this->isGranted('ROLE_ADMIN') || $mission->getGla() === $user->getName()) {
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
        } else {
            throw $this->createAccessDeniedException('Une fiche mission ne peut être supprimée que par la/le GLA qui l\'a créée ou par un·e admin.');
        }
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

        $newMissions = $repository->findByStatus([Mission::STATUS_DEFAULT], 'DESC');
        
        $nonNewStatuses = array_values(Mission::getStatuses()); // Array of possible statuses
        array_shift($nonNewStatuses);
        $otherMissions = $repository->findByStatus($nonNewStatuses, 'DESC');
        // $missions = $repository->findBy(array(), array('dateCreated' => 'DESC'));

        if (!$newMissions && !$otherMissions) {
            throw $this->createNotFoundException('Aucune fiche mission trouvée.');

        }

        return $this->render('mission/list.html.twig', array(
            'newMissions' => $newMissions,
            'otherMissions' => $otherMissions
        ));
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

        return $this->render('mission/recap.html.twig', array(
            'missions' => $missions
        ));
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
        if ($mission->getStatus() === Mission::STATUS_DEFAULT) {
            // Retrieve user and allow action only if user is a volunteer
            $this->denyAccessUnlessGranted('ROLE_VOLUNTEER');
            $user = $this->getUser();

            // Set volunteer, dateAssigned and status
            $mission->setVolunteer($user->getName());
            $mission->setDateAssigned(new \DateTime());
            $mission->setStatus(Mission::STATUS_ASSIGNED);

            // Persist changes to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($mission);
            $em->flush();

            $this->addFlash(
                'notice',
                'Vous avez pris en charge la mission.'
            );

            // Redirect to Mission view
            return $this->redirectToRoute('app_mission_view', array(
                'id' => $mission->getId()
            ));
        } else {
            throw new Exception('Seule une mission ayant le statut "'. Mission::STATUS_DEFAULT . '" peut être prise en charge par un·e bénévole.');
        }
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
        // Retrieve user and allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('ROLE_GLA');
        $user = $this->getUser();

        if($this->isGranted('ROLE_ADMIN') || $mission->getGla() === $user->getName()) {
            $status = $mission->getStatus();

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
            return $this->redirectToRoute('app_mission_view', array(
                'id' => $mission->getId()
            ));
        } else {
            throw $this->createAccessDeniedException('Une fiche mission ne peut être fermée ou rouverte que par la/le GLA l\'ayant créée ou par un·e admin.');
        }
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
        // Retrieve user and allow action only if user is admin or mission's gla
        $this->denyAccessUnlessGranted('ROLE_GLA');
        $user = $this->getUser();

        if($this->isGranted('ROLE_ADMIN') || $mission->getGla() === $user->getName()) {
            // Delete file from server
            $fileName = $mission->getAttachment();
            $fileUploader->delete($fileName);

            // Empty field in database
            $mission->setAttachment(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($mission);
            $em->flush();

            // Set a "flash" success message
            $this->addFlash(
                'notice',
                'La pièce jointe a bien été supprimée.'
            );

            return $this->redirectToRoute('app_mission_view', array(
                    'id' => $mission->getId()
            ));
        } else {
            throw $this->createAccessDeniedException('La PJ d\'une fiche mission ne peut être supprimée que par la/le GLA ayant créé la mission ou par un·e admin.');
        }
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
        $html = $this->renderView('pdf/mission-view.html.twig', array(
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

        $html = $this->renderView('pdf/mission-recap.html.twig', array(
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