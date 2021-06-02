<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Form\SpotType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/spots")
 */
class SpotController extends BaseController
{
    /**
     * @Route("/mapa/{deporte}", name="mapa")
     * @Route("/mapa/{deporte}/{provincia}", name="mapa_prov")
     */
    public function mapa(string $deporte, string $provincia = null)
    {
        $deporte = $this->depRepo->findOneBy(['nombre' => $deporte]);
        $spots = $this->spotRepo->findBy(['deporte' => $deporte, 'aprobado' => true]);

        if ($provincia) {
            $provincia = $this->provRepo->findOneBy(['nombre' => $provincia]);
        }

        return $this->render('spot/mapa.html.twig', [
            'spots' => $spots,
            'deporte' => $deporte,
            'provincia' => $provincia,
        ]);
    }

    /**
     * @Route("/listado/{deporte}", name="spots")
     * @Route("/listado/{deporte}/{provincia}", name="spots_prov")
     * @param Request $request
     * @param string $deporte
     * @param string|null $provincia
     * @return Response
     */
    public function listado(Request $request, string $deporte, string $provincia = null): Response
    {
        $spotsPagina = 8;
        $order = $request->get('order', 'fecha');
        $pagina = intval($request->get('p', 1));

        $deporte = $this->depRepo->findOneBy(['nombre' => $deporte]);
        if ($provincia) {
            $provincia = $this->provRepo->findOneBy(['nombre' => $request->get('provincia')]);
            $spots = $this->spotRepo->findSpotsBy($pagina, $spotsPagina, $deporte, $order, $provincia);
        } else {
            $spots = $this->spotRepo->findSpotsBy($pagina, $spotsPagina, $deporte, $order);
        }
        $maxPaginas = ceil(count($spots) / $spotsPagina);

        return $this->render('spot/listado.html.twig', [
            'spots' => $spots,
            'deporte' => $deporte,
            'provincia' => $provincia,
            'pagina' => $pagina,
            'maxPaginas' => $maxPaginas
        ]);
    }

    /**
     * @Route("/nuevo", name="spot_new")
     * @Route("/nuevo/{deporte}", name="dep_spot_new")
     * @Route("/nuevo/{deporte}/{provincia}", name="dep_prov_spot_new")
     * @param Request $request
     * @param string|null $deporte
     * @param string|null $provincia
     * @return Response
     */
    public function nuevoSpot(Request $request, string $deporte = null, string $provincia = null): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        if ($request->isXmlHttpRequest()) {
            $prov = $this->provRepo->find($request->get('prov'));

            if ($prov) {
                return $this->json(['coord' => $prov->getCoord()]);
            }
        }

        $spot = new Spot();
        if ($deporte) {
            $spot->setDeporte($this->depRepo->findOneBy(['nombre' => $deporte]));
        }
        if ($provincia) {
            $spot->setProvincia($this->provRepo->findOneBy(['nombre' => $provincia]));
        }
        $spot->setUser($this->getUser());
        $form = $this->createForm(SpotType::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spot = $form->getData();

            $this->em->persist($spot);
            $this->em->flush();
            return $this->redirectToRoute('spot_view', ['id' => $spot->getId()]);
        }

        return $this->render('spot/nuevo.html.twig', [
            'deporte' => $deporte,
            'provincia' => $provincia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="spot_edit")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editarSpot(Request $request, int $id): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        if ($request->isXmlHttpRequest()) {
            $prov = $this->provRepo->find($request->get('prov'));

            if ($prov) {
                return $this->json(['coord' => $prov->getCoord()]);
            }
        }

        $spot = $this->spotRepo->find($id);
        $form = $this->createForm(SpotType::class, $spot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $spot->setAprobado(null);
            $this->em->flush();
            return $this->redirectToRoute('spot_view', ['id' => $spot->getId()]);
        }

        return $this->render('spot/nuevo.html.twig', [
            'spot' => $spot,
            'form' => $form->createView(),
            'editar' => true,
        ]);
    }

    /**
     * @Route("/ver/{id}", name="spot_view")
     * @param int $id
     * @return Response
     */
    public function verSpot(int $id): Response
    {
        $spot = $this->spotRepo->find($id);

        return $this->render('spot/ver.html.twig', [
            'spot' => $spot,
            'deporte' => $spot->getDeporte()->getNombre(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="spot_remove")
     * @param string $deporte
     * @param int $id
     * @return RedirectResponse
     */
    public function eliminarSpot(int $id): RedirectResponse
    {
        $spot = $this->spotRepo->find($id);

        if (!$this->getUser()) {
            return $this->redirectToRoute('index');
        }

        if ($spot->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'No puedes eliminar un spot que no es tuyo');
            return $this->redirectToRoute('index');
        }

        $this->em->remove($spot);
        $this->em->flush();

        $this->addFlash('success', 'Spot eliminado con éxito.');
        return $this->redirectToRoute('index');
    }
}
