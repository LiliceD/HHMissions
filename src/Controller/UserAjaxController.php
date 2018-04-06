<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users/ajax")
 */
class UserAjaxController extends Controller
{
	/**
     * Send a text Response with a list of users in $category and whose name matches $search
     *
     * @Route(
     *  "/suggestions",
     *  name="app_user_suggestions"
     * )
     */
    public function searchSuggestions(Request $request)
    {
        // Get request parameters
        $category = $request->request->get('category');
        $search = $request->request->get('search');

        // Get all users of the requested category, ordered alphabetically
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findNameByCategory($category);
        
        $matchingUsers = [];

        if ($search) {
            // Fill an array with volunteers/GLAs matching search
            foreach($users as $user) {
                $userName = $user['name'];
                if (stripos($userName, $search) > -1) {
                    array_push($matchingUsers, $userName);
                }
            }
        } else {
            // Or with all volunteers/GLAs if no search input
            foreach($users as $user) {
                $userName = $user['name'];
                array_push($matchingUsers, $userName);
            }
        }

        // Convert array to text and send response
        $response = new Response();
        $response->setContent(implode('|', $matchingUsers));
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}