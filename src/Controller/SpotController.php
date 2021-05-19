<?php

namespace App\Controller;

use App\Entity\Provincia;
use App\Entity\Spot;
use App\Form\SpotType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spots/{deporte}")
 */
class SpotController extends BaseController
{
    /**
     * @Route("", name="spots")
     * @param Request $request
     * @param string $deporte
     * @return Response
     */
    public function listado(Request $request, string $deporte): Response
    {
        $spotsPagina = 10;
        $pagina = intval($request->get('p', 1));

        $deporte = $this->depRepo->findOneBy(['nombre' => $deporte]);
        $spots = $this->spotRepo->findSpotsBy($pagina, $spotsPagina, $deporte, 'fecha');
        $maxPaginas = ceil(count($spots) / $spotsPagina);

        return $this->render('spot/listado.html.twig', [
            'spots' => $spots,
            'deporte' => $deporte,
            'pagina' => $pagina,
            'maxPaginas' => $maxPaginas
        ]);
    }

    /**
     * @Route("/nuevo", name="spot_new")
     * @param Request $request
     * @param string $deporte
     * @return Response
     */
    public function nuevoSpot(Request $request, string $deporte): Response
    {
        if ($request->isXmlHttpRequest()) {
            $provRepo = $this->getDoctrine()->getRepository(Provincia::class);
            $provincia = $provRepo->find($request->get('provincia'));

            if ($provincia) {
                return $this->json(['coord' => $provincia->getCoord()]);
            }
        }

        $d = $this->depRepo->findOneBy(['nombre' => $deporte]);

        $spot = new Spot();
        $spot->setDeporte($d);
        $spot->setUser($this->getUser());
        $form = $this->createForm(SpotType::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spot = $form->getData();

            $em = $this->em;
            $em->persist($spot);
            $em->flush();
            return $this->redirectToRoute('spot_view', ['deporte' => $deporte, 'id' => $spot->getId()]);
        }

        return $this->render('spot/nuevo.html.twig', [
            'deporte' => $deporte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="spot_view")
     * @param string $deporte
     * @param int $id
     * @return Response
     */
    public function verSpot(string $deporte, int $id): Response
    {
        $spot = $this->spotRepo->find($id);

        return $this->render('spot/ver.html.twig', [
            'spot' => $spot,
            'deporte' => $deporte,
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="spot_remove")
     * @param string $deporte
     * @param int $id
     * @return RedirectResponse
     */
    public function eliminarSpot(string $deporte, int $id): RedirectResponse
    {
        $spot = $this->spotRepo->find($id);

        if ($spot->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'No puedes eliminar un spot que no es tuyo');
            return $this->redirectToRoute('index');
        }

        $em = $this->em;
        $em->remove($spot);
        $em->flush();

        $this->addFlash('success', 'Spot eliminado con éxito.');
        return $this->redirectToRoute('index');
    }
}
