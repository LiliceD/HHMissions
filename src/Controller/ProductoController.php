<?php

namespace App\Controller;

use App\Entity\Producto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductoController extends Controller
{
    /**
     * @Route("/producto", name="producto")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $producto = new Producto();
        $producto->setName('Keyboard');
        $producto->setPrice(19.99);
        $producto->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Producto (no queries yet)
        $em->persist($producto);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return new Response('Saved new producto with id '.$producto->getId());
    }

	/**
	 * @Route("/producto/{id}", name="producto_show")
	 */
	public function showAction($id)
	{
	    $producto = $this->getDoctrine()
	        ->getRepository(Producto::class)
	        ->find($id);

	    if (!$producto) {
	        throw $this->createNotFoundException(
	            'No producto found for id '.$id
	        );
	    }

	    return new Response('Check out this great producto: '.$producto->getName());
	}
}
