<?php

namespace App\Controller;

use App\Entity\Deporte;
use App\Entity\Provincia;
use App\Entity\Spot;
use App\Repository\DeporteRepository;
use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $depRepo;
    private $spotRepo;

    public function __construct(DeporteRepository $depRepo, SpotRepository $spotRepo)
    {
        $this->depRepo = $depRepo;
        $this->spotRepo = $spotRepo;
    }

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $provRepo = $this->getDoctrine()->getRepository(Provincia::class);
        $provincias = [];
        foreach ($provRepo->findAll() as $p) {
            $provincias[] = $p->getNombre();
        }

        return $this->render('home/index.html.twig', [
            'deportes' => $this->depRepo->findAll(),
            'spots' => $this->spotRepo->findAll(),
            'provincias' => $provincias,
        ]);
    }

    /**
     * @Route("/filtrarSpots", name="filtrar_spots", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function filtrarSpots(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $deporte = $this->depRepo->findOneBy(['nombre' => $request->get('deporte')]);
            $bestSpots = $this->spotRepo->findBestSpots(5, $deporte);

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
