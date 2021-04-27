<?php

namespace App\Controller;

use App\Repository\DeporteRepository;
use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotController extends AbstractController
{
    /**
     * @Route("/{deporte}", name="spots")
     * @param SpotRepository $spotRepository
     * @param DeporteRepository $deporteRepository
     * @param $deporte
     * @return Response
     */
    public function index(SpotRepository $spotRepository, DeporteRepository $deporteRepository, $deporte): Response
    {
        $findDeporte = $deporteRepository->findOneBy(['nombre' => $deporte]);
        $spots = $spotRepository->findBy(['deporte' => $findDeporte]);

        return $this->render('spot/index.html.twig', [
            'spots' => $spots,
            'deporte' => $deporte,
        ]);
    }

    /**
     * @Route("/{deporte}/{id}", name="spot_view")
     * @param SpotRepository $spotRepository
     * @param $deporte
     * @param $id
     * @return Response
     */
    public function spot(SpotRepository $spotRepository, $deporte, $id): Response
    {
        $spot = $spotRepository->find($id);

        return $this->render('spot/view.html.twig', [
            'spot' => $spot,
            'deporte' => $deporte,
        ]);
    }
}
