<?php

namespace App\Controller;

use App\Entity\Deporte;
use App\Entity\Spot;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use function MongoDB\BSON\toJSON;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $depRepo = $this->getDoctrine()->getRepository(Deporte::class);
        $spotRepo = $this->getDoctrine()->getRepository(Spot::class);

        return $this->render('home/index.html.twig', [
            'deportes' => $depRepo->findAll(),
            'spots' => $spotRepo->findAll(),
            'bestSpots' => $spotRepo->findBestSpots(5),
        ]);
    }

    /**
     * @Route("/filtrarSpot", name="filtrar_spots", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function filtrarBestSpots(Request $request): JsonResponse
    {
        $depRepo = $this->getDoctrine()->getRepository(Deporte::class);
        $spotRepo = $this->getDoctrine()->getRepository(Spot::class);

        if ($request->isXmlHttpRequest()) {
            $deporte = $depRepo->findOneBy(['nombre' => $request->get('deporte')]);
            $bestSpots = $spotRepo->findBestSpots(5, $deporte);

            $spots = [];
            foreach ($bestSpots as $bs) {
                $spots[] = [
                    'id' => $bs->getId(),
                    'nombre' => $bs->getNombre(),
                    'notaMedia' => $bs->getNotaMedia(),
                    'provincia' => $bs->getProvincia()->getNombre(),
                    'deporte' => $bs->getDeporte()->getNombre(),
                    'user' => $bs->getUser()->getUsername(),
                ];
            }

            return $this->json($spots);
        }

        throw $this->createNotFoundException();
    }
}
