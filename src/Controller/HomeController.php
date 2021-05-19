<?php

namespace App\Controller;

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
    public function busqueda(Request $request)
    {
        $provincia = $this->provRepo->findOneBy(['nombre' => $request->get('provincia')]);
        $deporte = $this->depRepo->findOneBy(['nombre' => $request->get('deporte')]);

        if (!$provincia) {
            $this->addFlash('warning', 'La provincia no coincide. Inténtelo de nuevo');
            return $this->redirectToRoute('index');
        }

        $spots = $this->spotRepo->findBy([
            'deporte' => $deporte,
            'provincia' => $provincia,
            'aprobado' => true
        ]);

        return $this->render('home/busqueda.html.twig', [
            'deporte' => $deporte,
            'provincia' => $provincia,
            'spots' => $spots,
        ]);
    }

// @Route("/revision/{id}", name="revisar_spot")

    /**
     * @Route("/revision", name="revision")
     * @return Response
     */
    public function revision(Request $request)
    {
        $spotsPendientes = $this->spotRepo->findBy(['aprobado' => null]);
        if (!$spotsPendientes) {
            dump($spotsPendientes);
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
        $spotsPagina = 5;
        $pagina = intval($request->get('p', 1));

        if ($request->isXmlHttpRequest()) {
            $deporte = $this->depRepo->findOneBy(['nombre' => $request->get('deporte')]);
            $orden = $request->get('orden');
            $bestSpots = $this->spotRepo->findSpotsBy($pagina, $spotsPagina, $deporte, $orden);

            $spots = [];
            foreach ($bestSpots as $bs) {
                $spots[] = [
                    'id' => $bs->getId(),
                    'nombre' => $bs->getNombre(),
                    'fecha' => date_format($bs->getFecha(), 'd/m/y'),
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
