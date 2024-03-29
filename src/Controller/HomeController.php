<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        // SERVICIO TEMPORAL (LISTENER/SUBSCRIBER)
        if ($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();
            $user->setUltimoAcceso(new DateTime());
            $this->em->flush();
        }

        $provincias = [];
        foreach ($this->provRepo->findAll() as $p) {
            $provincias[] = $p->getNombre();
        }

        return $this->render('home/index.html.twig', [
            'deportes' => $this->depRepo->findAll(),
            'spots' => $this->spotRepo->findAll(),
            'provincias' => $provincias,
        ]);
    }

    /**
     * @Route("/busqueda", name="busqueda", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function busqueda(Request $request): Response
    {
        $provincia = $this->provRepo->findOneBy(['nombre' => $request->get('provincia')]);
        if (!$provincia) {
            $this->addFlash('warning', 'La provincia no coincide. Inténtelo de nuevo');
            return $this->redirectToRoute('index');
        }

        return $this->redirectToRoute('spots_prov', [
            'deporte' => $request->get('deporte'),
            'provincia' => $provincia->getNombre(),
        ]);
    }

    /**
     * @Route("/revision", name="revision")
     * @param Request $request
     * @return Response
     */
    public function revision(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user || !$user->puedeRevisar()) {
            $this->addFlash('danger', 'No tienes permisos para revisar.');
            return $this->redirectToRoute('index');
        }

        $spotsPendientes = $this->spotRepo->findBy(['aprobado' => null]);
        if (empty($spotsPendientes)) {
            $this->addFlash('info', 'No hay spots en revisión en estos momentos. Inténtelo de nuevo más tarde.');
            return $this->redirectToRoute('index');
        }

        $spot = $spotsPendientes[0];
        if ($request->get('aprobar') || $request->get('rechazar')) {
            $opcion = $request->get('aprobar') !== null;
            $spot->setAprobado($opcion);

            $revision = $request->get('comentario');
            if ($request->get('rechazar') && $revision !== '') {
                $spot->setRevision($revision);
            }

            $this->em->flush();
            return $this->redirectToRoute('revision');
        }

        return $this->render('home/revision.html.twig', [
            'spot' => $spot,
            'spotsPendientes' => count($spotsPendientes)
        ]);
    }

    /**
     * @Route("/filtrarSpots", name="filtrar_spots", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function filtrarSpots(Request $request): JsonResponse
    {
        $spotsPagina = 3;
        $pagina = intval($request->get('p', 1));

        if ($request->isXmlHttpRequest()) {
            $deporte = $this->depRepo->findOneBy(['nombre' => $request->get('deporte')]);
            $orden = $request->get('orden');
            $bestSpots = $this->spotRepo->findSpotsBy($pagina, $spotsPagina, $deporte, $orden);

            $spots = [];
            /** @var Spot $bs */
            foreach ($bestSpots as $bs) {
                $spots[] = [
                    'id' => $bs->getId(),
                    'nombre' => $bs->getNombre(),
                    'fecha' => date_format($bs->getFecha(), 'd/m/y'),
                    'notaMedia' => $bs->getNotaMedia(),
                    'numValoraciones' => count($bs->getValoraciones()),
                    'provincia' => $bs->getProvincia()->getNombre(),
                    'deporte' => $bs->getDeporte()->getNombre(),
                    'user' => $bs->getUser()->getUsername(),
                    'coord' => $bs->getCoord(),
                ];
            }

            return $this->json($spots);
        }

        throw $this->createNotFoundException();
    }
}
