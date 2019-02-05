<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionSearchByIdType;
use App\Form\MissionType;
use App\Form\MissionSearchType;
use App\Manager\MissionManager;
use App\Utils\Constant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MissionController
 *
 * @Route("/missions")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
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
     *     "/ajouter",
     *     name="app_mission_new"
     * )
     *
     * @param Request $request
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function new(Request $request, MissionManager $missionManager)
    {
        $form = $this->createForm(MissionType::class, new Mission());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mission = $form->getData();
            $missionManager->create($mission);

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
     *     "/{id}/voir/",
     *     name="app_mission_view",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission $mission
     *
     * @return Response
     */
    public function view(Mission $mission): Response
    {
        return $this->render('mission/view.html.twig', [
            'mission' => $mission
        ]);
    }

    /**
     * @Route(
     *     "/{id}/modifier/",
     *     name="app_mission_edit",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission        $mission
     * @param Request        $request
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse|Response
     */
    public function edit(Mission $mission, Request $request, MissionManager $missionManager)
    {
        $this->denyAccessUnlessGranted('simpleEdit', $mission);
        
        $oldMission = clone $mission;
        $mission = $missionManager->setAttachmentAsFile($mission);

        // Create form
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $missionManager->update($mission, $oldMission, $this->getUser());

            return $this->redirectToRoute('app_mission_view', [
                'id' => $mission->getId()
            ]);
        }

        return $this->render('mission/edit.html.twig', [
            'form' => $form->createView(),
            'mission' => $mission,
            'attachmentUrl' => $oldMission->getAttachment(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/supprimer/",
     *     name="app_mission_delete",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission        $mission
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse
     */
    public function delete(Mission $mission, MissionManager $missionManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('edit', $mission);

        $flash = $missionManager->delete($mission);

        $this->addFlash($flash->getType(), $flash->getMessage());

        return $this->redirectToRoute('app_mission_list');
    }

    /**
     * Recap of opened (= any status but "closed") missions
     *
     * @Route(
     *     "/recap/",
     *     name="app_mission_recap",
     * )
     *
     * @return Response
     */
    public function recap(): Response
    {
        $searchForm = $this->createForm(MissionSearchType::class);

        return $this->render('mission/recap.html.twig', [
            'form' => $searchForm->createView(),
        ]);
    }


//  █████╗      ██╗ █████╗ ██╗  ██╗
// ██╔══██╗     ██║██╔══██╗╚██╗██╔╝
// ███████║     ██║███████║ ╚███╔╝
// ██╔══██║██   ██║██╔══██║ ██╔██╗
// ██║  ██║╚█████╔╝██║  ██║██╔╝ ██╗
// ╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═╝  ╚═╝


    /**
     * @Route(
     *     "/filter",
     *     name="app_mission_filter"
     * )
     *
     * @param Request        $request
     * @param MissionManager $missionManager
     *
     * @return JsonResponse
     */
    public function cGet(Request $request, MissionManager $missionManager): JsonResponse
    {
        $filters = $request->request->all();
        $missions = $missionManager->getFilteredMissions($filters);
        $missionsJson = $missionManager->getJsonMissions($missions);
        $data = ['missions' => $missionsJson];

        return new JsonResponse($data);
    }


// ██████╗  █████╗  ██████╗ ███████╗███████╗
// ██╔══██╗██╔══██╗██╔════╝ ██╔════╝██╔════╝
// ██████╔╝███████║██║  ███╗█████╗  ███████╗
// ██╔═══╝ ██╔══██║██║   ██║██╔══╝  ╚════██║
// ██║     ██║  ██║╚██████╔╝███████╗███████║
// ╚═╝     ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝

    /**
     * @Route(
     *     "/{activity}",
     *     name="app_mission_list"
     * )
     *
     * @param Request        $request
     * @param String         $activity
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse|Response
     */
    public function list(Request $request, MissionManager $missionManager, String $activity = Constant::ACTIVITY_GLA)
    {
        // Create search by id form
        $searchFormById = $this->createForm(MissionSearchByIdType::class);
        $searchFormById->handleRequest($request);

        if ($searchFormById->isSubmitted() && $searchFormById->isValid()) {
            // Get id submitted
            $data = $searchFormById->getData();
            $id = $data['id'];

            // Redirect to mission
            if ($missionManager->getById($id)) {
                return $this->redirectToRoute('app_mission_view', [
                    'id' => $id,
                ]);
            }

            // If not mission found, set a "flash" error message and redirect to list
            $this->addFlash(
                'error',
                'Il n\'y a pas de fiche mission n°'.$id
            );

            return $this->redirectToRoute('app_mission_list', [
                'activity' => $activity,
            ]);
        }

        $fullSearchForm = $this->createForm(MissionSearchType::class);

        return $this->render('mission/list.html.twig', [
            'form' => $searchFormById->createView(),
            'searchForm' => $fullSearchForm->createView(),
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
     *     "/{id}/assigner/",
     *     name="app_mission_assign",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission        $mission
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function assign(Mission $mission, MissionManager $missionManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('assign', $mission);

        $flash = $missionManager->assignVolunteer($mission, $this->getUser());

        $this->addFlash($flash->getType(), $flash->getMessage());

        return $this->redirectToRoute('app_mission_view', [
            'id' => $mission->getId(),
        ]);
    }

    /**
     * Close / re-open a mission if its current status is finished / closed respectively
     *
     * @Route(
     *     "/{id}/fermer/",
     *     name="app_mission_close",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission        $mission
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse
     */
    public function close(Mission $mission, MissionManager $missionManager): RedirectResponse
    {
        $flash = $missionManager->close($mission);

        $this->addFlash($flash->getType(), $flash->getMessage());

        return $this->redirectToRoute('app_mission_view', [
            'id' => $mission->getId(),
        ]);
    }

    /**
     * Empty field "attachment" and delete file
     *
     * @Route(
     *     "/{id}/supprimer-pj/",
     *     name="app_mission_attachment-delete",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission        $mission
     * @param MissionManager $missionManager
     *
     * @return RedirectResponse
     */
    public function deleteAttachment(Mission $mission, MissionManager $missionManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('edit', $mission);

        $flash = $missionManager->deleteAttachment($mission);

        $this->addFlash($flash->getType(), $flash->getMessage());

        return $this->redirectToRoute('app_mission_view', [
                'id' => $mission->getId(),
        ]);
    }

    /**
     * Export a given mission to PDF
     *
     * @Route(
     *     "/{id}/pdf/",
     *     name="app_mission_pdf-export",
     *     requirements={"id"="\d+"}
     * )
     *
     * @param Mission $mission
     *
     * @return Response
     */
    public function pdfExport(Mission $mission): Response
    {
        $html = $this->renderView('pdf/mission-view.html.twig', [
            'mission' => $mission
        ]);
        $filename = sprintf('fm-%s__%s.pdf', $mission->getId(), $mission->getFormattedDateCreated());

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            Response::HTTP_OK,
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
     *     "/recap/pdf/{activity}",
     *     name="app_mission_recap_pdf-export"
     * )
     *
     * @param string         $activity
     * @param Request        $request
     * @param MissionManager $missionManager
     *
     * @return Response
     */
    public function recapPdfExport(string $activity, Request $request, MissionManager $missionManager): Response
    {
        $dateMin = $request->query->get('dateMin');
        $dateMax = $request->query->get('dateMax');
        $statuses = Mission::STATUS_DEFAULT.'|'.Mission::STATUS_ASSIGNED.'|'.Mission::STATUS_FINISHED;
        $filters = [
            'activity' => $activity,
            'statuses' => $statuses,
            'dateCreatedMin' => $dateMin,
            'dateCreatedMax' => $dateMax,
        ];

        $missions = $missionManager->getFilteredMissions($filters);
        $html = $this->renderView('pdf/mission-recap.html.twig', [
            'missions' => $missions
        ]);
        $filename = sprintf('fm-recap_%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, [
                'page-size' => 'A3',
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
