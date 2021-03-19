<?php

namespace App\Controller;

use App\Entity\Provincia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $provRepo = $this->getDoctrine()->getRepository(Provincia::class);
        $provincias = $provRepo->findAll();

        return $this->render('home/index.html.twig', [
            'provincias' => $provincias,
        ]);
    }
}
