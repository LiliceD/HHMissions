<?php

namespace App\Controller;

use App\Entity\Accomodation;
use App\Entity\Mission;
use App\Form\MissionType;
use App\Form\MissionSearchType;
use App\Service\FileUploader; // to use FileUploader service in edit
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security; // for @Security annotations
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception; // to throw new Exception()
use Symfony\Component\HttpFoundation\File\File; // for new File() in edit
use Symfony\Component\HttpFoundation\JsonResponse; // app_mission_filter
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
        
        // Copy all previous data
        $oldMission = $mission;

        // Save previous attachment file name to avoid delete by edit with no changes
        $oldFileName = $oldMission->getAttachment();

        // Set file from file name
        if ($oldFileName) {
            $mission->setAttachment(
                new File($this->getParameter('app_attachment_directory').'/'.$oldFileName)
            );
        }

        // Create form
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve user to check authorizations
            $user = $this->getUser();

            // DateCreated, accomodation and GLA can't be changed
            $mission->setDateCreated($oldMission->getDateCreated());
            $mission->setAccomodation($oldMission->getAccomodation());
            $mission->setGla($oldMission->getGla());

            // DateAssigned and Volunteer can only be changed by admin
            if (!$this->isGranted('ROLE_ADMIN', $user)) {
                $mission->setDateAssigned($oldMission->getDateAssigned());
                $mission->setVolunteer($oldMission->getVolunteer());
            }

            // DateFinished can only be changed by volunteer assigned or admin
            if (!$this->isGranted('ROLE_VOLUNTEER', $user)) {
                $mission->setDateFinished($oldMission->getDateFinished());
            }

            // Description and Info can only be changed by GLA or admin
            if (!$this->isGranted('ROLE_GLA', $user)) {
                $mission->setDescription($oldMission->getDescription());
                $mission->setInfo($oldMission->getInfo());
            }

            // Update status
            $mission->updateStatus();

            // Manage attachment
            $file = $mission->getAttachment();

            if ($file && $this->isGranted('ROLE_GLA', $user)) {
                $fileName = $fileUploader->upload($file);

                $mission->setAttachment($fileName);
            } else if ($oldFileName) {
                $mission->setAttachment($oldFileName);
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
            'attachmentUrl' => $oldFileName
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
     *  "/",
     *  name="app_mission_list",
     * )
     */
    public function list(Request $request)
    {    
        // Create search by id form
        $form = $this->createForm(MissionSearchType::class);
        $form->handleRequest($request);

        // Get Mission repository
        $repository = $this->getDoctrine()->getRepository(Mission::class);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get id submitted 
            $data = $form->getData();
            $id = $data['id'];

            // Retrieve mission
            $mission = $repository->findOneBy(['id' => $id]);

            if ($mission) {
                return $this->redirectToRoute('app_mission_view', [
                    'id' => $mission->getId()
                ]);
            }

            // Set a "flash" error message
            $this->addFlash(
                'error',
                'Il n\'y a pas de fiche mission n°'.$id
            );

            return $this->redirectToRoute('app_mission_list');
        }

        // Retrieve missions not finished
        $missions = $repository->findByStatusJoined([Mission::STATUS_DEFAULT, Mission::STATUS_ASSIGNED]);
        
        // Split missions based on their status
        $newMissions = [];
        $assignedMissions = [];

        foreach($missions as $mission) {
            switch ($mission->getStatus()) {
                case Mission::STATUS_DEFAULT:
                    array_push($newMissions, $mission);
                    break;
                case Mission::STATUS_ASSIGNED:
                    array_push($assignedMissions, $mission);
            }
        }

        return $this->render('mission/list.html.twig', [
            'form' => $form->createView(),
            'newMissions' => $newMissions,
            'assignedMissions' => $assignedMissions,
            'searchForm' => $form->createView()
        ]);
    }

    /**
     * Recap of opened (= any status but "closed") missions
     *
     * @Route(
     *  "/recap/",
     *  name="app_mission_recap",
     * )
     */
    public function recap()
    {    
        // Create search form
        $searchForm = $this->createForm(MissionSearchType::class);

        return $this->render('mission/recap.html.twig', [
            'form' => $searchForm->createView()
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

        $filename = sprintf('fm-%s__%s.pdf', $mission->getId(), $mission->getFormattedDateCreated());

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
     *  "/recap/pdf/{dateMin}/{dateMax}",
     *  name="app_mission_recap_pdf-export"
     * )
     */
    public function recapPdfExport($dateMin = '', $dateMax = '')
    {
        // Create array of filters
        $filters = [];

        if ($dateMin || $dateMax) {
            $dateCreatedFilter = ['field' => 'm.dateCreated', 'value' => ['min' => $dateMin, 'max' => $dateMax]];
            array_push($filters, $dateCreatedFilter);
        }

        // Add filter on status (can't manage to send it with JavaScript)
        $statuses = array_values(Mission::getStatuses()); // Array of all statuses
        array_pop($statuses); // Remove STATUS_CLOSED

        $statusFilter = ['field' => 'm.status', 'value' => $statuses];
        array_push($filters, $statusFilter);

        // Retrieve Missions matching filters
        $missions = $this->getDoctrine()
            ->getRepository(Mission::class)
            ->findByFiltersJoined($filters)
        ;

        $html = $this->renderView('pdf/mission-recap.html.twig', [
            'missions' => $missions
        ]);

        $filename = sprintf('fm-recap_%s.pdf', date('Y-m-d'));

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


//  █████╗      ██╗ █████╗ ██╗  ██╗
// ██╔══██╗     ██║██╔══██╗╚██╗██╔╝
// ███████║     ██║███████║ ╚███╔╝ 
// ██╔══██║██   ██║██╔══██║ ██╔██╗ 
// ██║  ██║╚█████╔╝██║  ██║██╔╝ ██╗
// ╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═╝  ╚═╝


    /**
     * @Route(
     *  "/filter",
     *  name="app_mission_filter",
     * )
     */
    public function filter(Request $request)
    {
        // Get request referer
        $referer = $request->headers->get('referer');
        // Get filters from request parameters
        $glaIds = $request->request->get('glaIds');
        $volunteerIds = $request->request->get('volunteerIds');
        $accomodationIds = $request->request->get('accomodationIds');
        $descriptionSearch = $request->request->get('descriptionSearch');
        $dateCreatedMin = $request->request->get('dateCreatedMin');
        $dateCreatedMax = $request->request->get('dateCreatedMax');
        $dateFinishedMin = $request->request->get('dateFinishedMin');
        $dateFinishedMax = $request->request->get('dateFinishedMax');

        // Create array of filters
        $filters = [];

        if ($glaIds) {
            $glaFilter = ['field' => 'g.id', 'value' => $glaIds];
            array_push($filters, $glaFilter);
        }
        
        if ($volunteerIds) {
            $volunteerFilter = ['field' => 'v.id', 'value' => $volunteerIds];
            array_push($filters, $volunteerFilter);
        }
        
        if ($accomodationIds) {
            $accomodationFilter = ['field' => 'a.id', 'value' => $accomodationIds];
            array_push($filters, $accomodationFilter);
        }

        if ($descriptionSearch) {
            // Query will search for missions'whose description *contains* $descriptionSearch
            $descriptionFilter = ['field' => 'm.description', 'value' => '%'.$descriptionSearch.'%'];
            array_push($filters, $descriptionFilter);
        }

        if ($dateCreatedMin || $dateCreatedMax) {
            $dateCreatedFilter = ['field' => 'm.dateCreated', 'value' => ['min' => $dateCreatedMin, 'max' => $dateCreatedMax]];
            array_push($filters, $dateCreatedFilter);
        }

        if ($dateFinishedMin || $dateFinishedMax) {
            $dateFinishedFilter = ['field' => 'm.dateFinished', 'value' => ['min' => $dateFinishedMin, 'max' => $dateFinishedMax]];
            array_push($filters, $dateFinishedFilter);
        }

        // Add filter on status (can't manage to send it with JavaScript) and order
        if (preg_match('/\/missions\/$/', $referer)) {
            // From missions list : only finished and closed
            $statuses = [Mission::STATUS_FINISHED, Mission::STATUS_CLOSED];

            $statusFilter = ['field' => 'm.status', 'value' => $statuses];
            array_push($filters, $statusFilter);
        } else if (preg_match('/\/recap\/$/', $referer)) {
            // From mission recap : all but closed
            $statuses = array_values(Mission::getStatuses()); // Array of all statuses
            array_pop($statuses); // Remove STATUS_CLOSED

            $statusFilter = ['field' => 'm.status', 'value' => $statuses];
            array_push($filters, $statusFilter);
        }
        
        // Retrieve Missions matching filters
        $missions = $this->getDoctrine()
            ->getRepository(Mission::class)
            ->findByFiltersJoined($filters)
        ;

        // Convert Missions to JSON
        $missionsJson = [];

        foreach ($missions as $mission) {
            $missionJson = [
                // Path to mission view
                'url' => $this->generateUrl('app_mission_view', ['id' => $mission->getId()]),
                // Mission properties
                'id' => $mission->getId(),
                'status' => $mission->getStatus(),
                'dateCreated' => $mission->getFormattedDateCreated(),
                'dateAssigned' => $mission->getDateAssigned() ? $mission->getFormattedDateAssigned() : null,
                'dateFinished' => $mission->getDateFinished() ? $mission->getFormattedDateFinished() : null,
                'description' => $mission->getDescription(),
                'conclusions' => $mission->getConclusions(),
                // Gla
                'gla' => [
                    'id' => $mission->getGla()->getId(),
                    'name' => $mission->getGla()->getName()
                ],
                // Accomodation
                'accomodation' => [
                    'id' => $mission->getAccomodation()->getId(),
                    'name' => $mission->getAccomodation()->getName(),
                    'street' => $mission->getAccomodation()->getStreet(),
                    'postalCode' => $mission->getAccomodation()->getPostalCode(),
                    'city' => $mission->getAccomodation()->getCity()
                ]
            ];

            if ($mission->getVolunteer()) {
                // Volunteer
                $volunteerJson = ['volunteer' => [
                    'id' => $mission->getVolunteer()->getId(),
                    'name' => $mission->getVolunteer()->getName()
                ]];

                $missionJson = array_merge($missionJson, $volunteerJson);
            }

            array_push($missionsJson, $missionJson);
        }

        // Convert path to mission recap pdf export to JSON
        $recapJson = ['url' => $this->generateUrl('app_mission_recap_pdf-export', [
            'dateMin' => $dateCreatedMin,
            'dateMax'=> $dateCreatedMax
        ])];

        // Combine recap and missions
        $data = ['missions' => $missionsJson, 'recap' => $recapJson]; 

        // Return JSON response
        return new JsonResponse($data);
    }
}
